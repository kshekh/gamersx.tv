<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\{ HeaderUtils, Request, Response, ResponseHeaderBag, RedirectResponse };
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

    public function __construct(SerializerInterface $serializer, Filesystem $filesystem, StorageInterface $storage)
    {
        $this->serializer = $serializer;
        $this->filesystem = $filesystem;
        $this->storage = $storage;
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
                            $archiveImageName = $hashedName.'-custom.img';
                            $archive->extractTo($tmp, $archiveImageName);

                            $imagePath = $this->storage->resolvePath($row, 'customArtFile', $this->admin->getClass());
                            $this->filesystem->copy("$tmp/$archiveImageName", $imagePath, TRUE);
                            $this->filesystem->remove("$tmp/$archiveImageName");
                        }

                        if ($row->getOverlayArt() !== null) {
                            $tmp = sys_get_temp_dir();
                            $archiveImageName = $hashedName.'-overlay.img';
                            $archive->extractTo($tmp, $archiveImageName);

                            $imagePath = $this->storage->resolvePath($row, 'overlayArtFile', $this->admin->getClass());
                            $this->filesystem->copy("$tmp/$archiveImageName", $imagePath, TRUE);
                            $this->filesystem->remove("$tmp/$archiveImageName");
                        }

                        $this->admin->getModelManager()->create($row);
                        $success += 1;
                    }
                }
                $archive->close();

                $this->addFlash('sonata_flash_success', "Successfully imported $success home rows.");
            } catch (\Exception $e) {
                $this->addFlash('sonata_flash_error', 'Couldn\'t import Home Row file');

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
                $json = $this->serializer->serialize($selectedModel, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['partner', 'homeRow']]);
                $name = 'home-row-item-'.hash('sha1', $json);
                $archive->addFromString($name.'.json', $json);
                if ($selectedModel->getCustomArt()) {
                    $path = $this->storage->resolvePath($selectedModel, 'customArtFile', $this->admin->getClass());

                    $archive->addFile($path, $name.'-custom.img');
                }
                if ($selectedModel->getOverlayArt()) {
                    $path = $this->storage->resolvePath($selectedModel, 'overlayArtFile', $this->admin->getClass());
                    $archive->addFile($path, $name.'-overlay.img');
                }

            }

            $archive->close();

        } catch (\Exception $e) {
            $this->addFlash('sonata_flash_error', 'Couldn\'t create Zip file for export');

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
