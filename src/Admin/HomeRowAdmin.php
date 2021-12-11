<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\HomeRow;
use App\Form\SortAndTrimOptionsType;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Symfony\Component\Form\Extension\Core\Type\{ ChoiceType, HiddenType, NumberType, TimeType };
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


    public function configureActionButtons($action, $object = null)
    {
        $list = parent::configureActionButtons($action, $object);
        $list['importForm']['template'] = 'CRUD/import_button.html.twig';
        return $list;
    }

    public function getDashboardActions()
    {
        $actions = parent::getDashboardActions();

        $actions['importForm'] = [
            'label' => 'Import',
            'url' => $this->generateUrl('importForm'),
            'icon' => 'level-up',
        ];

        return $actions;
    }

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
            ->add('partner')
            ->add('isPublished')
            ->add('isGlowStyling')
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
            ->add('partner')
            ->add('options', null, [
                'editable' => false,
                'sortable' => false,
            ])
            ->add('isPublished', null, [
                'sortable' => false
            ])
            ->add('isGlowStyling', null, [
                'sortable' => false
            ])
            ->add('isPublishedStart', null, [
                'sortable' => false
            ])
            ->add('isPublishedEnd', null, [
                'sortable' => false
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
            ])
            ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('title')
            ->add('sortIndex')
            ->add('layout', ChoiceType::class, [
                'choices' => [
                    'Classic Small' => 'ClassicSm',
                    'Classic Medium' => 'ClassicMd',
                    'Classic Large' => 'ClassicLg',
                    'Classic Vertical' => 'ClassicVertical',
                    'Full Width - Descriptive' => 'FullWidthDescriptive',
                    'Full Width - Imagery' => 'FullWidthImagery',
                    'Parallax' => 'Parallax',
                    'Numbered' => 'NumberedRow',
                ]
            ])
            ->add('partner')
            ->add('options', SortAndTrimOptionsType::class, [
                'label' => 'Sort and Trim Options',
                'required' => false,
            ])
            ->add('isPublished')
            ->add('isGlowStyling', ChoiceType::class, [
                'choices' => [
                    'Enabled' => 'Enabled',
                    'Disabled' => 'Disabled',
                    'Enabled if Live' => 'Enabled if Live'
                ]
            ])
            ->add('isPublished', null, [
                'help' => 'Current Server Time: ' . date('H:i')
            ])
            ->add('isPublishedStart', TimeType::class, [
                'label'=> 'Publish Start Time',
                'required' => false,
                'input'  => 'timestamp',
                'widget' => 'single_text',
                'attr'=> [
                    'class' => 'timepicker',
                    'title'=> "Start timepicker for published",
                ]
            ])
            ->add('isPublishedEnd', TimeType::class, [
                'label'=> 'Publish End Time',
                'required' => false,
                'input'  => 'timestamp',
                'widget' => 'single_text',
                'attr'=> [
                    'class' => 'timepicker',
                    'title'=> "End timepicker for published",
                ]
            ])
            ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('title')
            ->add('sortIndex')
            ->add('layout')
            ->add('partner')
            ->add('options')
            ->add('isPublished')
            ->add('isGlowStyling')
            ;
    }

    protected function configureBatchActions($actions)
    {

        if ($this->hasRoute('list') && $this->hasAccess('list')) {
            $actions['export'] = [
                'label' => 'Download Export Zip',
                'ask_confirmation' => FALSE,
            ];
        }

        return $actions;
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->add('reorder', $this->getRouterIdParameter().'/reorder')
            ->add('importForm')
            ->add('import')
            ;
    }

    public function alterNewInstance(object $instance): void
    {
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $roles = $user->getPartnerRoles();

        if (!$roles->isEmpty()) {
            $partner = $roles->first()->getPartner();

            if ($partner !== null) {
                $instance->setPartner($partner);
            }
        }
    }
}
