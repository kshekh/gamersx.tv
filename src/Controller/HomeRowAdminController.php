<?php

namespace App\Controller;

use App\HomeRow;

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

        $qb = $this->admin->getModelManager()->getEntityManager('App:HomeRow')->
            createQueryBuilder()
            ->addSelect('hr')
            ->from('App:HomeRow', 'hr');
            ;

        if ($direction === 'down') {
            $qb->where('hr.sortIndex >= :thisSort')
                ->add('orderBy', 'hr.sortIndex ASC');
        } elseif ($direction === 'up') {
            $qb->where('hr.sortIndex <= :thisSort')
                ->add('orderBy', 'hr.sortIndex DESC');
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
                    $this->addFlash('sonata_flash_success', "Swapped position for rows ".$object->getTitle()." and ".$row->getTitle());
                    break;
                }

            }
        } else {
            $this->addFlash('sonata_flash_error', "Moving ".$object->getTitle()." that way is not possible.");
        }

        return new RedirectResponse(
            $this->admin->generateUrl('list', ['filter' => $this->admin->getFilterParameters()])
        );
    }
}
