<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ Request, Response, RedirectResponse };
use Symfony\Component\Routing\Annotation\Route;
use Sonata\AdminBundle\Controller\CRUDController;

class HomeRowAdminController extends CRUDController
{
    /**
     * @Route("/admin/{id}/reorder", name="admin_app_homerow_reorder")
     */
    public function reorderAction(Request $request, $id): Response
    {
        $object = $this->admin->getSubject();
        $direction = $request->get('direction');

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
        }

        $this->addFlash('sonata_flash_success', "Reordered successfully $direction");

        return new RedirectResponse(
            $this->admin->generateUrl('list', ['filter' => $this->admin->getFilterParameters()])
        );
    }
}
