<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\HomeRow;
use App\Entity\HomeRowItem;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Symfony\Component\Form\Extension\Core\Type\{ ChoiceType, HiddenType, NumberType };
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

final class HomeRowAdmin extends AbstractAdmin
{

    protected function configureTabMenu(MenuItemInterface $menu, $action,
        AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, ['edit', 'show'])) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;
        $id = $admin->getRequest()->get('id');

        $menu->addChild('View Row', $admin->generateMenuUrl('show', ['id' => $id]));

        if ($this->isGranted('EDIT')) {
            $menu->addChild('Edit Row', $admin->generateMenuUrl('edit', ['id' => $id]));
        }

        if ($this->isGranted('LIST')) {
            $menu->addChild('Manage Items',
                $admin->generateMenuUrl('admin.home_row_item.list', ['id' => $id]));
        }
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('title')
            ->add('itemType')
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('title')
            ->add('itemType')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('title')
            ->add('sort', ChoiceType::class, [
                'choices' => [
                    'Ascending Popularity' => HomeRow::SORT_ASC,
                    'Descending Popularity' => HomeRow::SORT_DESC,
                    'Fixed Order' => HomeRow::SORT_FIXED,
                ]
            ])
        ->add('itemType', ChoiceType::class, [
            'choices' => [
                'Games' => HomeRow::ITEM_TYPE_GAME,
                'Streamers' => HomeRow::ITEM_TYPE_STREAMER,
                'Popular' => HomeRow::ITEM_TYPE_POPULAR,
            ]
        ])
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('title')
            ->add('sort')
            ->add('itemType')
            ;
    }
}
