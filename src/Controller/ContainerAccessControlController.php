<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\{
    JsonResponse,
    Request};
use Symfony\Component\Serializer\SerializerInterface;
use Sonata\AdminBundle\Controller\CRUDController;

class ContainerAccessControlController extends CRUDController
{
    private SerializerInterface $serializer;
    private Filesystem $filesystem;
    private EntityManagerInterface $em;

    public function __construct(SerializerInterface $serializer, Filesystem $filesystem, EntityManagerInterface $em)
    {
        $this->serializer = $serializer;
        $this->filesystem = $filesystem;
        $this->em = $em;
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
            $this->em->flush();

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
            $this->em->flush();
            $this->addFlash('sonata_flash_success', $msg);
            $return = ['status'=> 1,'msg'=> $msg];

        }
        return new JsonResponse($return);
    }
}
