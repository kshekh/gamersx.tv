<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\HomeRow;
use App\Form\SortAndTrimOptionsType;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Symfony\Component\Form\Extension\Core\Type\{ ChoiceType, HiddenType, NumberType };
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

final class HomeRowAdmin extends AbstractAdmin
{

    public function createQuery($context = 'list'): ProxyQuery
    {
        /** @var ProxyQuery $query */
        $query = parent::createQuery($context);

        return $query
            ->setSortOrder('ASC')
            ->setSortBy([], ['fieldName' => 'sortIndex'])
            ;
    }

    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'sortIndex'
    );

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
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('title', null, [
                'sortable' => false
            ])
            ->add('sortIndex', null, [
                'editable' => true,
                'sortable' => false,
            ])
            ->add('layout', null, [
                'editable' => false,
                'sortable' => false,
            ])
            ->add('options', null, [
                'editable' => false,
                'sortable' => false,
            ])
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                    'moveUp' => [
                        'template' => 'CRUD/list__action_reorder.html.twig',
                        'direction' => 'up'
                    ],
                    'moveDown' => [
                        'template' => 'CRUD/list__action_reorder.html.twig',
                        'direction' => 'down'
                    ],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('title')
            ->add('sortIndex')
            ->add('layout', ChoiceType::class, [
                'choices' => [
                    'Classic' => HomeRow::LAYOUT_CLASSIC,
                    'Custom Background' => HomeRow::LAYOUT_CUSTOM_BG_ART,
                    'Custom Vertical Frame' => HomeRow::LAYOUT_CUSTOM_FRAME_ART,
                    'Numbered' => HomeRow::LAYOUT_NUMBERED,
                ]
            ])
            ->add('options', SortAndTrimOptionsType::class, [
                'label' => 'Sort and Trim Options',
                'required' => false,
            ])
            ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('title')
            ->add('sortIndex')
            ->add('layout')
            ->add('options')
            ;
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->add('reorder', $this->getRouterIdParameter().'/reorder');
    }
}
