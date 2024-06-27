<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\{
    JsonResponse,
    Request};
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

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function removeBlacklistedContainer(Request $request,$id): JsonResponse
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

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function fullSiteBlacklistedContainer(Request $request,$id): JsonResponse
    {
        $request->request->all();
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
