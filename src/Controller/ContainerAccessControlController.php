<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\{
    JsonResponse,
    Request};
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ContainerAccessControlController extends CrudController
{
    private SerializerInterface $serializer;
    private Filesystem $filesystem;
    private EntityManagerInterface $entityManager;

    public function __construct(SerializerInterface $serializer, Filesystem $filesystem, EntityManagerInterface $entityManager)
    {
        $this->serializer = $serializer;
        $this->filesystem = $filesystem;
        $this->entityManager = $entityManager;
    }


    public function batchActionExport(ProxyQueryInterface $selectedModelQuery, Request $request): Response
    {
        $this->admin->checkAccess('list');
        $selectedModels = $selectedModelQuery->execute();

        $archive = new \ZipArchive();
        $filename = tempnam(sys_get_temp_dir(), 'export_');
        $zipName = 'export_' . date('Y-m-d_H-i-s') . '.zip';

        try {
            if ($archive->open($filename, \ZipArchive::CREATE) !== true) {
                throw new \RuntimeException('Failed to create ZIP file');
            }

            foreach ($selectedModels as $selectedModel) {
                $json = $this->serializer->serialize($selectedModel, 'json', [
                    AbstractNormalizer::IGNORED_ATTRIBUTES => ['partner', 'items'],
                    'circular_reference_handler' => function ($object) {
                        return $object->getId();
                    }
                ]);
                $archive->addFromString($selectedModel->getId() . '.json', $json);
            }

            $archive->close();

        } catch (\Exception $e) {
            $this->addFlash('sonata_flash_error', 'Couldn\'t create Zip file for export: ' . $e->getMessage());

            return new RedirectResponse(
                $this->admin->generateUrl('list', [
                    'filter' => $this->admin->getFilterParameters()
                ])
            );
        }

        $response = new BinaryFileResponse($filename);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $zipName
        );

        // Optionally delete the file after sending it
        $response->deleteFileAfterSend(true);

        return $response;
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function removeBlacklistedContainerAction(Request $request,$id): JsonResponse
    {
        $request->request->all();
        $object = $this->admin->getSubject();
        if (!$object) {
            $return = ['status'=> 0,'msg' => sprintf('unable to find the object with id: %s', $id)];
        } else {
            $object->setIsBlacklisted(0);
            $this->entityManager->flush();

            $msg = 'Container removed from blacklist.';
            $this->addFlash('sonata_flash_success', $msg);
            $return = ['status'=> 1,'msg'=> $msg];

        }
        return new JsonResponse($return);
    }

    public function fullSiteBlacklistedContainerAction(Request $request,$id): JsonResponse
    {
        $data = $request->request->all();
        $return = [];
        $object = $this->admin->getSubject();
        if (!$object) {
            $return = ['status'=> 0,'msg'=>sprintf('unable to find the object with id: %s', $id)];
        } else {
            $is_full_site_blacklisted =  $object->getIsFullSiteBlacklisted();
            if($is_full_site_blacklisted == 1) {
                $object->setIsFullSiteBlacklisted(null);
                $msg = 'Container removed from full site blacklist.';
            } else {
                $object->setIsFullSiteBlacklisted(1);
                $msg = 'Container added to full site blacklist.';
            }
            $this->entityManager->flush();
            $this->addFlash('sonata_flash_success', $msg);
            $return = ['status'=> 1,'msg'=> $msg];

        }
        return new JsonResponse($return);
    }
}
