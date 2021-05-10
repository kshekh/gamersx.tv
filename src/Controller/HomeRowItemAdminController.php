<?php

namespace App\Controller;

use App\HomeRowItem;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ Request, Response, RedirectResponse };
use Symfony\Component\Routing\Annotation\Route;
use Sonata\AdminBundle\Controller\CRUDController;

class HomeRowItemAdminController extends CRUDController
{
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
}
