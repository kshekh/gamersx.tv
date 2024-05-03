<?php

namespace App\Controller;

use App\Entity\HomeRowItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\{HeaderUtils,
    JsonResponse,
    Request,
    Response,
    ResponseHeaderBag,
    RedirectResponse};
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

class ContainerAccessControlController extends CRUDController
{
    private $serializer;
    private $filesystem;
    private EntityManagerInterface $em;

    public function __construct(SerializerInterface $serializer, Filesystem $filesystem, EntityManagerInterface $em)
    {
        $this->serializer = $serializer;
        $this->filesystem = $filesystem;
        $this->em = $em;
    }

    /**
     * @param $id
     */
    public function removeBlacklistedContainerAction(Request $request,$id): JsonResponse
    {
        $data = $request->request->all();
        $return = [];
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
     * @param $id
     */
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
            $this->em->flush();
            $this->addFlash('sonata_flash_success', $msg);
            $return = ['status'=> 1,'msg'=> $msg];

        }
        return new JsonResponse($return);
    }
}
