<?php

namespace App\Controller;

use App\Entity\HomeRowItem;
use App\Service\YouTubeApi;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\AdminBundle\Exception\LockException;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Sonata\AdminBundle\Exception\ModelManagerThrowable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\{
    File\UploadedFile,
    HeaderUtils,
    Request,
    Response,
    ResponseHeaderBag,
    RedirectResponse};
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Vich\UploaderBundle\Storage\StorageInterface;


class HomeRowItemAdminController extends CRUDController
{
    private $serializer;
    private $filesystem;
    private $storage;
    private $youtube;
    private $em;

    public function __construct(SerializerInterface $serializer, Filesystem $filesystem, StorageInterface $storage,YouTubeApi $youtube, EntityManagerInterface $em)
    {
        $this->serializer = $serializer;
        $this->filesystem = $filesystem;
        $this->storage = $storage;
        $this->youtube = $youtube;
        $this->em = $em;
    }

    public function createAction()
    {
        $request = $this->getRequest();

        $this->assertObjectExists($request);

        $this->admin->checkAccess('create');

        // the key used to lookup the template
        $templateKey = 'edit';

        $class = new \ReflectionClass($this->admin->hasActiveSubClass() ? $this->admin->getActiveSubClass() : $this->admin->getClass());

        if ($class->isAbstract()) {
            return $this->renderWithExtraParams(
                '@SonataAdmin/CRUD/select_subclass.html.twig',
                [
                    'base_template' => $this->getBaseTemplate(),
                    'admin' => $this->admin,
                    'action' => 'create',
                ],
                null
            );
        }

        $newObject = $this->admin->getNewInstance();

        $preResponse = $this->preCreate($request, $newObject);
        if (null !== $preResponse) {
            return $preResponse;
        }

        $this->admin->setSubject($newObject);

        $form = $this->admin->getForm();

        $form->setData($newObject);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
                /** @phpstan-var T $submittedObject */
                $submittedObject = $form->getData();
                $this->admin->setSubject($submittedObject);
                $this->admin->checkAccess('create', $submittedObject);
                if($submittedObject->getItemType() == HomeRowItem::TYPE_YOUTUBE_PLAYLIST) {
                    $submittedObject->setIsPublished(false);
                }
                $submittedObject->setUpdatedAt(new \DateTime());

                $maxContainers = (int)$submittedObject->getSortAndTrimOptions()['maxContainers'];
                $maxLive = (int)$submittedObject->getSortAndTrimOptions()['maxLive'];

                try {
                    $newObject = $this->admin->create($submittedObject);

                    if($submittedObject->getItemType() == HomeRowItem::TYPE_YOUTUBE_PLAYLIST) {
                        $youtube = $this->youtube;
                        parse_str(parse_url($submittedObject->getPlaylistId(), PHP_URL_QUERY), $parameters);
                        $playlistId = $parameters['list'];

                        $play_list_items = $youtube->getPlaylistItemsInfo($playlistId)->getItems();

                        $offline_video_array = [];
                        $live_video_array = [];
                        $final_video_array = [];

                        foreach ($play_list_items as $pl_item => $pl_item_data) {
                            $videoId = $pl_item_data->getSnippet()->getResourceId()->getVideoId();
                            $videoId_link = 'https://www.youtube.com/watch?v=' . $videoId;

                            $channelId = $pl_item_data->getSnippet()->getVideoOwnerChannelId();
                            $broadcast = $youtube->getLiveChannel($channelId)->getItems();
                            $broadcast = !empty($broadcast) ? $broadcast[0] : NULL;
                            if ($broadcast) {
                                $live_video_array[] = [
                                    'video_link' => $videoId_link,
                                    'channel_id' => $channelId
                                ];
                            } else {
                                $offline_video_array[] = [
                                    'video_link' => $videoId_link,
                                    'channel_id' => $channelId
                                ];
                            }
                        }

                        foreach ($live_video_array as $live_item => $live_data) {
                            if((count($final_video_array) <= $maxLive) && (count($final_video_array) <= $maxContainers) && !empty($maxLive)) {
                                $final_video_array[] = $live_data;
                            }
                        }

                        foreach ($offline_video_array as $offline_item => $offline_data) {
                            if((count($final_video_array) < $maxContainers) && !empty($maxContainers)) {
                                $final_video_array[] = $offline_data;
                            }
                        }

                        foreach ($final_video_array as $final_item => $final_data) {

                            $videoId_link = $final_data['video_link'];

                            $submittedObjectVideo = new HomeRowItem();
                            $submittedObjectVideo->setHomeRow($submittedObject->getHomeRow());
                            $submittedObjectVideo->setPartner($submittedObject->getPartner());
                            $submittedObjectVideo->setLabel($submittedObject->getLabel());
                            $submittedObjectVideo->setSortIndex($submittedObject->getSortIndex());
                            $submittedObjectVideo->setItemType(HomeRowItem::TYPE_YOUTUBE_VIDEO);
                            $submittedObjectVideo->setSortAndTrimOptions($submittedObject->getSortAndTrimOptions());
                            $submittedObjectVideo->setShowArt($submittedObject->getShowArt());
                            $submittedObjectVideo->setCustomArt($submittedObject->getCustomArt());
                            $submittedObjectVideo->setOverlayArt($submittedObject->getOverlayArt());
                            $submittedObjectVideo->setOfflineDisplayType($submittedObject->getOfflineDisplayType());
                            $submittedObjectVideo->setLinkType($submittedObject->getLinkType());
                            $submittedObjectVideo->setTopic($submittedObject->getTopic());
                            $submittedObjectVideo->setCustomLink($submittedObject->getCustomLink());
                            $submittedObjectVideo->setIsPublished(true);
                            $submittedObjectVideo->setDescription($submittedObject->getDescription());
                            $submittedObjectVideo->setIsPublishedStart($submittedObject->getIsPublishedStart());
                            $submittedObjectVideo->setIsPublishedEnd($submittedObject->getIsPublishedEnd());
                            $submittedObjectVideo->setIsPartner($submittedObject->getIsPartner());
                            $submittedObjectVideo->setVideoId($videoId_link);
                            $submittedObjectVideo->setPlaylistId($newObject->getId());
                            $submittedObjectVideo->setUpdatedAt($submittedObject->getUpdatedAt());

                            $this->em->persist($submittedObjectVideo);
                            $this->em->flush();
                        }
                    }

                    if ($this->isXmlHttpRequest()) {
                        return $this->handleXmlHttpRequestSuccessResponse($request, $newObject);
                    }

                    $this->addFlash(
                        'sonata_flash_success',
                        $this->trans(
                            'flash_create_success',
                            ['%name%' => $this->escapeHtml($this->admin->toString($newObject))],
                            'SonataAdminBundle'
                        )
                    );

                    // redirect to edit mode
                    return $this->redirectTo($newObject);
                } catch (ModelManagerException $e) {
                    // NEXT_MAJOR: Remove this catch.
                    $this->handleModelManagerException($e);

                    $isFormValid = false;
                } catch (ModelManagerThrowable $e) {
                    $this->handleModelManagerThrowable($e);

                    $isFormValid = false;
                }
            }

            // show an error message if the form failed validation
            if (!$isFormValid) {
                if ($this->isXmlHttpRequest() && null !== ($response = $this->handleXmlHttpRequestErrorResponse($request, $form))) {
                    return $response;
                }

                $this->addFlash(
                    'sonata_flash_error',
                    $this->trans(
                        'flash_create_error',
                        ['%name%' => $this->escapeHtml($this->admin->toString($newObject))],
                        'SonataAdminBundle'
                    )
                );
            } elseif ($this->isPreviewRequested()) {
                // pick the preview template if the form was valid and preview was requested
                $templateKey = 'preview';
                $this->admin->getShow();
            }
        }

        $formView = $form->createView();
        // set the theme for the current Admin Form
        $this->setFormTheme($formView, $this->admin->getFormTheme());

        // NEXT_MAJOR: Remove this line and use commented line below it instead
        $template = $this->admin->getTemplate($templateKey);
        // $template = $this->templateRegistry->getTemplate($templateKey);

        return $this->renderWithExtraParams($template, [
            'action' => 'create',
            'form' => $formView,
            'object' => $newObject,
            'objectId' => null,
        ]);
    }

    public function editAction($deprecatedId = null) // NEXT_MAJOR: Remove the unused $id parameter
    {
        if (isset(\func_get_args()[0])) {
            @trigger_error(sprintf(
                'Support for the "id" route param as argument 1 at `%s()` is deprecated since'
                .' sonata-project/admin-bundle 3.62 and will be removed in 4.0,'
                .' use `AdminInterface::getIdParameter()` instead.',
                __METHOD__
            ), \E_USER_DEPRECATED);
        }

        // the key used to lookup the template
        $templateKey = 'edit';

        $request = $this->getRequest();
        $this->assertObjectExists($request, true);

        $id = $request->get($this->admin->getIdParameter());
        \assert(null !== $id);
        $existingObject = $this->admin->getObject($id);
        \assert(null !== $existingObject);

        $this->checkParentChildAssociation($request, $existingObject);

        $this->admin->checkAccess('edit', $existingObject);

        $preResponse = $this->preEdit($request, $existingObject);
        if (null !== $preResponse) {
            return $preResponse;
        }

        $this->admin->setSubject($existingObject);
        $objectId = $this->admin->getNormalizedIdentifier($existingObject);

        $form = $this->admin->getForm();

        $form->setData($existingObject);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
                /** @phpstan-var T $submittedObject */
                $submittedObject = $form->getData();
                $this->admin->setSubject($submittedObject);
                if($submittedObject->getItemType() == HomeRowItem::TYPE_YOUTUBE_PLAYLIST) {
                    $submittedObject->setIsPublished(false);
                }

                $maxContainers = (int)$submittedObject->getSortAndTrimOptions()['maxContainers'];
                $maxLive = (int)$submittedObject->getSortAndTrimOptions()['maxLive'];

                try {
                    $existingObject = $this->admin->update($submittedObject);

                    if ($this->isXmlHttpRequest()) {
                        return $this->handleXmlHttpRequestSuccessResponse($request, $existingObject);
                    }

                    if($submittedObject->getItemType() == HomeRowItem::TYPE_YOUTUBE_PLAYLIST) {
                        $youtube = $this->youtube;
                        parse_str(parse_url($submittedObject->getPlaylistId(), PHP_URL_QUERY), $parameters);
                        $playlistId = $parameters['list'];

                        $get_playlist_videos =  $this->em->getRepository(HomeRowItem::class)->findBy(['itemType'=>'youtube_video','playlistId'=>$submittedObject->getId()]);
                        foreach ($get_playlist_videos as $get_playlist_video_data){
                            $this->em->remove($get_playlist_video_data);
                            $this->em->flush();
                        }
                        $play_list_items = $youtube->getPlaylistItemsInfo($playlistId)->getItems();

                        $offline_video_array = [];
                        $live_video_array = [];
                        $final_video_array = [];

                        foreach ($play_list_items as $pl_item => $pl_item_data) {
                            $videoId = $pl_item_data->getSnippet()->getResourceId()->getVideoId();
                            $videoId_link = 'https://www.youtube.com/watch?v=' . $videoId;

                            $channelId = $pl_item_data->getSnippet()->getVideoOwnerChannelId();
                            $broadcast = $youtube->getLiveChannel($channelId)->getItems();
                            $broadcast = !empty($broadcast) ? $broadcast[0] : NULL;
                            if ($broadcast) {
                                $live_video_array[] = [
                                    'video_link' => $videoId_link,
                                    'channel_id' => $channelId
                                ];
                            } else {
                                $offline_video_array[] = [
                                    'video_link' => $videoId_link,
                                    'channel_id' => $channelId
                                ];
                            }
                        }

                        foreach ($live_video_array as $live_item => $live_data) {
                            if((count($final_video_array) <= $maxLive) && (count($final_video_array) <= $maxContainers) && !empty($maxLive)) {
                                $final_video_array[] = $live_data;
                            }
                        }

                        foreach ($offline_video_array as $offline_item => $offline_data) {
                            if((count($final_video_array) < $maxContainers) && !empty($maxContainers)) {
                                $final_video_array[] = $offline_data;
                            }
                        }

                        foreach ($final_video_array as $final_item => $final_data) {

                            $videoId_link = $final_data['video_link'];
                            $submittedObjectVideo = new HomeRowItem();
                            $submittedObjectVideo->setHomeRow($submittedObject->getHomeRow());
                            $submittedObjectVideo->setPartner($submittedObject->getPartner());
                            $submittedObjectVideo->setLabel($submittedObject->getLabel());
                            $submittedObjectVideo->setSortIndex($submittedObject->getSortIndex());
                            $submittedObjectVideo->setItemType(HomeRowItem::TYPE_YOUTUBE_VIDEO);
                            $submittedObjectVideo->setSortAndTrimOptions($submittedObject->getSortAndTrimOptions());
                            $submittedObjectVideo->setShowArt($submittedObject->getShowArt());
                            $submittedObjectVideo->setCustomArt($submittedObject->getCustomArt());
                            $submittedObjectVideo->setOverlayArt($submittedObject->getOverlayArt());
                            $submittedObjectVideo->setOfflineDisplayType($submittedObject->getOfflineDisplayType());
                            $submittedObjectVideo->setLinkType($submittedObject->getLinkType());
                            $submittedObjectVideo->setTopic($submittedObject->getTopic());
                            $submittedObjectVideo->setCustomLink($submittedObject->getCustomLink());
                            $submittedObjectVideo->setIsPublished(true);
                            $submittedObjectVideo->setDescription($submittedObject->getDescription());
                            $submittedObjectVideo->setIsPublishedStart($submittedObject->getIsPublishedStart());
                            $submittedObjectVideo->setIsPublishedEnd($submittedObject->getIsPublishedEnd());
                            $submittedObjectVideo->setIsPartner($submittedObject->getIsPartner());
                            $submittedObjectVideo->setVideoId($videoId_link);
                            $submittedObjectVideo->setPlaylistId($submittedObject->getId());
                            $submittedObjectVideo->setUpdatedAt($submittedObject->getUpdatedAt());

                            $this->em->persist($submittedObjectVideo);
                            $this->em->flush();
                        }
                    }

                    $this->addFlash(
                        'sonata_flash_success',
                        $this->trans(
                            'flash_edit_success',
                            ['%name%' => $this->escapeHtml($this->admin->toString($existingObject))],
                            'SonataAdminBundle'
                        )
                    );

                    // redirect to edit mode
                    return $this->redirectTo($existingObject);
                } catch (ModelManagerException $e) {
                    // NEXT_MAJOR: Remove this catch.
                    $this->handleModelManagerException($e);

                    $isFormValid = false;
                } catch (ModelManagerThrowable $e) {
                    $this->handleModelManagerThrowable($e);

                    $isFormValid = false;
                } catch (LockException $e) {
                    $this->addFlash('sonata_flash_error', $this->trans('flash_lock_error', [
                        '%name%' => $this->escapeHtml($this->admin->toString($existingObject)),
                        '%link_start%' => sprintf('<a href="%s">', $this->admin->generateObjectUrl('edit', $existingObject)),
                        '%link_end%' => '</a>',
                    ], 'SonataAdminBundle'));
                }
            }

            // show an error message if the form failed validation
            if (!$isFormValid) {
                if ($this->isXmlHttpRequest() && null !== ($response = $this->handleXmlHttpRequestErrorResponse($request, $form))) {
                    return $response;
                }

                $this->addFlash(
                    'sonata_flash_error',
                    $this->trans(
                        'flash_edit_error',
                        ['%name%' => $this->escapeHtml($this->admin->toString($existingObject))],
                        'SonataAdminBundle'
                    )
                );
            } elseif ($this->isPreviewRequested()) {
                // enable the preview template if the form was valid and preview was requested
                $templateKey = 'preview';
                $this->admin->getShow();
            }
        }

        $formView = $form->createView();
        // set the theme for the current Admin Form
        $this->setFormTheme($formView, $this->admin->getFormTheme());

        // NEXT_MAJOR: Remove this line and use commented line below it instead
        $template = $this->admin->getTemplate($templateKey);
        // $template = $this->templateRegistry->getTemplate($templateKey);

        return $this->renderWithExtraParams($template, [
            'action' => 'edit',
            'form' => $formView,
            'object' => $existingObject,
            'objectId' => $objectId,
        ]);
    }

    public function deleteAction($id) // NEXT_MAJOR: Remove the unused $id parameter
    {
        $request = $this->getRequest();
        $this->assertObjectExists($request, true);

        $id = $request->get($this->admin->getIdParameter());
        \assert(null !== $id);
        $object = $this->admin->getObject($id);
        \assert(null !== $object);

        $this->checkParentChildAssociation($request, $object);

        $this->admin->checkAccess('delete', $object);

        $preResponse = $this->preDelete($request, $object);
        if (null !== $preResponse) {
            return $preResponse;
        }

        if (Request::METHOD_DELETE === $request->getMethod()) {
            // check the csrf token
            $this->validateCsrfToken('sonata.delete');

            $objectName = $this->admin->toString($object);

            try {
                $playlist_id = $object->getId();
                $this->admin->delete($object);

                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(['result' => 'ok'], Response::HTTP_OK, []);
                }

                if($object->getItemType() == HomeRowItem::TYPE_YOUTUBE_PLAYLIST) {
                    $get_playlist_videos = $this->em->getRepository(HomeRowItem::class)->findBy(['itemType' => 'youtube_video', 'playlistId' => $playlist_id ]);
                    foreach ($get_playlist_videos as $get_playlist_video_data) {
                        $this->em->remove($get_playlist_video_data);
                        $this->em->flush();
                    }
                }

                $this->addFlash(
                    'sonata_flash_success',
                    $this->trans(
                        'flash_delete_success',
                        ['%name%' => $this->escapeHtml($objectName)],
                        'SonataAdminBundle'
                    )
                );
            } catch (ModelManagerException $e) {
                // NEXT_MAJOR: Remove this catch.
                $this->handleModelManagerException($e);

                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(['result' => 'error'], Response::HTTP_OK, []);
                }

                $this->addFlash(
                    'sonata_flash_error',
                    $this->trans(
                        'flash_delete_error',
                        ['%name%' => $this->escapeHtml($objectName)],
                        'SonataAdminBundle'
                    )
                );
            } catch (ModelManagerThrowable $e) {
                $this->handleModelManagerThrowable($e);

                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(['result' => 'error'], Response::HTTP_OK, []);
                }

                $this->addFlash(
                    'sonata_flash_error',
                    $this->trans(
                        'flash_delete_error',
                        ['%name%' => $this->escapeHtml($objectName)],
                        'SonataAdminBundle'
                    )
                );
            }

            return $this->redirectTo($object);
        }

        // NEXT_MAJOR: Remove this line and use commented line below it instead
        $template = $this->admin->getTemplate('delete');
        // $template = $this->templateRegistry->getTemplate('delete');

        return $this->renderWithExtraParams($template, [
            'object' => $object,
            'action' => 'delete',
            'csrf_token' => $this->getCsrfToken('sonata.delete'),
        ]);
    }

    private function setFormTheme(FormView $formView, ?array $theme = null): void
    {
        $twig = $this->get('twig');

        $twig->getRuntime(FormRenderer::class)->setTheme($formView, $theme);
    }

    private function checkParentChildAssociation(Request $request, object $object): void
    {
        if (!$this->admin->isChild()) {
            return;
        }

        // NEXT_MAJOR: remove this check
        if (!$this->admin->getParentAssociationMapping()) {
            return;
        }

        $parentAdmin = $this->admin->getParent();
        $parentId = $request->get($parentAdmin->getIdParameter());

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $propertyPath = new PropertyPath($this->admin->getParentAssociationMapping());

        $parentAdminObject = $parentAdmin->getObject($parentId);
        $objectParent = $propertyAccessor->getValue($object, $propertyPath);

        // $objectParent may be an array or a Collection when the parent association is many to many.
        $parentObjectMatches = $this->equalsOrContains($objectParent, $parentAdminObject);

        if (!$parentObjectMatches) {
            // NEXT_MAJOR: make this exception
            @trigger_error(
                'Accessing a child that isn\'t connected to a given parent is deprecated since sonata-project/admin-bundle 3.34 and won\'t be allowed in 4.0.',
                \E_USER_DEPRECATED
            );
        }
    }

    private function equalsOrContains($haystack, object $needle): bool
    {
        if ($needle === $haystack) {
            return true;
        }

        if (is_iterable($haystack)) {
            foreach ($haystack as $haystackItem) {
                if ($haystackItem === $needle) {
                    return true;
                }
            }
        }

        return false;
    }

    public function reorderAction(Request $request, $id, $childId=null): Response
    {
        $object = $this->admin->getSubject();
        $direction = $request->get('direction');

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
        }

        $qb = $this->admin->getModelManager()->getEntityManager('App:HomeRowItem')
            ->createQueryBuilder()
            ->addSelect('hri')
            ->from('App:HomeRowItem', 'hri')
            ->where('hri.homeRow = :rowId')
            ->setParameter('rowId', $object->getHomeRow())
            ;

        if ($direction === 'down') {
            $qb->andWhere('hri.sortIndex >= :thisSort')
                ->add('orderBy', 'hri.sortIndex ASC');
        } elseif ($direction === 'up') {
            $qb->andWhere('hri.sortIndex <= :thisSort')
                ->add('orderBy', 'hri.sortIndex DESC');
        }
        $qb->setParameter('thisSort', $object->getSortIndex());

        $rows = $qb->getQuery()->getResult();


        if (count($rows) > 1) {

            foreach ($rows as $row) {
                if ($row->getId() === $object->getId()) {
                    $current = $row->getSortIndex();
                } elseif (isset($current) && $row->getId() !== $object->getId()) {
                    $object->setSortIndex($row->getSortIndex());
                    $this->admin->getModelManager()->update($object);

                    $row->setSortIndex($current);
                    $this->admin->getModelManager()->update($row);
                    $this->addFlash('sonata_flash_success', "Swapped position for rows ".$object->getLabel()." and ".$row->getLabel());
                    break;
                }

            }
        } else {
            $this->addFlash('sonata_flash_error', "Moving ".$object->getLabel()." that way is not possible.");
        }

        return new RedirectResponse(
            $this->admin->generateUrl('list', ['filter' => $this->admin->getFilterParameters()])
        );
    }

    public function importFormAction(Request $request)
    {
        return $this->render('admin/import_form.html.twig');
    }

    public function importAction(Request $request)
    {
        $this->admin->checkAccess('create');
        $file = $request->files->get('import');

        $archive = new \ZipArchive();
        $archive->open($file);

        if ($archive) {
            try {
                $success = 0;
                for ( $i = 0; $i < $archive->numFiles; $i++ ) {
                    $stats = $archive->statIndex($i);
                    // Import the JSON files
                    if (($j = strpos($stats['name'], '.json')) > 0) {
                        $hashedName = substr($stats['name'], 0, $j);

                        $json = $archive->getFromIndex($i);
                        $row = $this->serializer->deserialize($json, $this->admin->getClass(), 'json');
                        $row->setIsPublished(FALSE);
                        $row->setPartner(NULL);

                        if ($row->getCustomArt() !== null) {
                            $tmp = sys_get_temp_dir();
                            $archiveCustomImageName = $hashedName.'-custom.img';
                            $archive->extractTo($tmp, $archiveCustomImageName);
                            $row->setCustomArtFile(new UploadedFile("$tmp/$archiveCustomImageName",$row->getCustomArt()));
                        }

                        if ($row->getOverlayArt() !== null) {
                            $tmp = sys_get_temp_dir();
                            $archiveOverlayImageName = $hashedName.'-overlay.img';
                            $archive->extractTo($tmp, $archiveOverlayImageName);
                            $row->setOverlayArtFile(new UploadedFile("$tmp/$archiveOverlayImageName", $row->getOverlayArt()));
                        }

                        $this->admin->getModelManager()->create($row);
                        $success += 1;
                    }
                }
                $archive->close();
                $this->addFlash('sonata_flash_success', "Successfully imported $success home rows.");
            } catch (\Exception $e) {
                $this->addFlash('sonata_flash_error', $e->getMessage());

                return new RedirectResponse(
                    $this->admin->generateUrl('list', [
                        'filter' => $this->admin->getFilterParameters()
                    ])
                );
            }
        }

        return new RedirectResponse(
            $this->admin->generateUrl('list', [
                'filter' => $this->admin->getFilterParameters()
            ])
        );
    }

    public function batchActionExport(ProxyQueryInterface $selectedModelQuery, Request $request): Response
    {
        $this->admin->checkAccess('list');
        $selectedModels = $selectedModelQuery->execute();

        $archive = new \ZipArchive();
        $filename = $this->filesystem->tempnam(sys_get_temp_dir(), 'export_');
        try {
            $archive->open($filename, \ZipArchive::CREATE);

            foreach ($selectedModels as $selectedModel) {
                if (!file_exists($selectedModel->getCustomArtFile())){
                    $selectedModel->setCustomArt(null);
                    $selectedModel->setCustomArtFile(null);
                }
                if (!file_exists($selectedModel->getOverlayArtFile())){
                    $selectedModel->setOverlayArt(null);
                    $selectedModel->setOverlayArtFile(null);
                }
                $json = $this->serializer->serialize($selectedModel, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['partner', 'homeRow']]);
                $name = 'home-row-item-'.hash('sha1', $json);
                $archive->addFromString($name.'.json', $json);
                if ($selectedModel->getCustomArt()) {
                    $path = $this->storage->resolvePath($selectedModel, 'customArtFile', $this->admin->getClass());
                    $content = file_get_contents($path);
                    $archive->addFromString(pathinfo ( $name.'-custom.img', PATHINFO_BASENAME), $content);
                    //$archive->addFile($path, $name.'-custom.img');
                }

                if ($selectedModel->getOverlayArt()) {
                    $path = $this->storage->resolvePath($selectedModel, 'overlayArtFile', $this->admin->getClass());
                    $content = file_get_contents($path);
                    $archive->addFromString(pathinfo ( $name.'-overlay.img', PATHINFO_BASENAME), $content);
                    //$archive->addFile($path, $name.'-overlay.img');
                }
            }

            $archive->close();
        } catch (\Exception $e) {
            $this->addFlash('sonata_flash_error', $e->getMessage());
            return new RedirectResponse(
                $this->admin->generateUrl('list', [
                    'filter' => $this->admin->getFilterParameters()
                ])
            );
        }

        $response = new BinaryFileResponse($filename);
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            'gamersx-home-row-item-export-'.time().'.zip'
        );
        $response->headers->set('Content-Disposition', $disposition);
        $response->deleteFileAfterSend(TRUE);
        return $response;
    }
}
