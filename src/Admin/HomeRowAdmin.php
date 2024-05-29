<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\HomeRow;
use App\Form\SortAndTrimOptionsType;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType, HiddenType, NumberType, TimeType, TimezoneType};
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class HomeRowAdmin extends AbstractAdmin
{
    private $tokenStorage;

    public function setTokenStorage(TokenStorageInterface $tokenStorage): void
    {
        $this->tokenStorage = $tokenStorage;
    }

//    public function createQuery($context = 'list'): ProxyQuery
//    {
//        /** @var ProxyQuery $query */
//        $query = parent::createQuery($context);
//
//        return $query
//            ->setSortOrder('ASC')
//            ->setSortBy([], ['fieldName' => 'sortIndex'])
//            ;
//    }

//    protected $datagridValues = array(
//        '_page' => 1,
//        '_sort_order' => 'ASC',
//        '_sort_by' => 'sortIndex'
//    );
    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues[DatagridInterface::PAGE] = 1;
        $sortValues[DatagridInterface::SORT_ORDER] = 'ASC';
        $sortValues[DatagridInterface::SORT_BY] = 'sortIndex';
    }

    public function configureActionButtons(array $buttonList, string $action, ?object $object = null): array
    {
        $list = parent::configureActionButtons($buttonList, $action, $object);
        $list['importForm']['template'] = 'CRUD/import_button.html.twig';
        return $list;
    }

//    public function getDashboardActions()
//    {
//        $actions = parent::getDashboardActions();
//
//        $actions['importForm'] = [
//            'label' => 'Import',
//            'url' => $this->generateUrl('importForm'),
//            'icon' => 'level-up',
//        ];
//
//        return $actions;
//    }

    protected function configureDashboardActions(array $actions): array
    {
        $actions['importForm'] = [
            'label' => 'Import',
            'url' => $this->generateUrl('importForm'),
            'icon' => 'level-up',
        ];

        return $actions;
    }

    protected function configureTabMenu(
        MenuItemInterface $menu,
        string $action,
        AdminInterface $childAdmin = null
    ): void
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
            $menu->addChild(
                'Manage Items',
                $admin->generateMenuUrl('admin.home_row_item.list', ['id' => $id])
            );
        }
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('title')
            ->add('partner')
            ->add('isPublished')
            ->add('isGlowStyling')
            ->add('onGamersXtv')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('title', null, [
                'editable' => true,
                'sortable' => false
            ])
            ->add('sortIndex', null, [
                'editable' => true,
                'sortable' => false
            ])
            ->add('layout', 'choice', [
                'editable' => true,
                'choices' => [
                    ['text' => 'Classic Small', 'value' => 'ClassicSm'],
                    ['text' => 'Classic Medium', 'value' => 'ClassicMd'],
                    ['text' => 'Classic Large', 'value' => 'ClassicLg'],
                    ['text' => 'Classic Vertical', 'value' => 'ClassicVertical'],
                    ['text' => 'Full Width - Descriptive', 'value' => 'FullWidthDescriptive'],
                    ['text' => 'Full Width - Imagery', 'value' => 'FullWidthImagery'],
                    ['text' => 'Parallax', 'value' => 'Parallax'],
                    ['text' => 'Numbered', 'value' => 'NumberedRow']
                ],
                'sortable' => false
            ])
            ->add('partner')
            ->add('options', null, [
                'editable' => false,
                'sortable' => false
            ])
            ->add('isPublished', null, [
                'editable' => true,
                'sortable' => false
            ])
            ->add('onGamersXtv', null, [
                'editable' => true,
                'sortable' => false
            ])
            ->add('isGlowStyling', null, [
                'sortable' => false
            ])
            ->add('timezone', null, [
                'sortable' => false
            ])
            ->add('isPublishedStart', null, [
                'editable' => true,
                'sortable' => false
            ])
            ->add('isPublishedEnd', null, [
                'editable' => true,
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

    protected function configureFormFields(FormMapper $form): void
    {
        $form
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
                    'Enabled If Live' => 'enabled_if_live',
                    'Always On' => 'always_on',
                    'Always Off' => 'always_off',
                    'Enabled If Offline' => 'enabled_if_offline'
                ]
            ])
            ->add('isCornerCut', ChoiceType::class, [
                'choices' => [
                    'Enabled If Live' => 'enabled_if_live',
                    'Always On' => 'always_on',
                    'Always Off' => 'always_off',
                    'Enabled If Offline' => 'enabled_if_offline'
                ]
            ])
            ->add('isPublished', null, [
                'help' => 'Current Server Time: ' . date('H:i')
            ])
            ->add('timezone', TimezoneType::class, ['required' => false])
            ->add('isPublishedStart', TimeType::class, [
                'label' => 'Publish Start Time',
                'required' => false,
                'input' => 'string',
                'input_format' => 'H:i:s',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'timepicker',
                    'title' => "Start timepicker for published",
                ]
            ])
            ->add('isPublishedEnd', TimeType::class, [
                'label' => 'Publish End Time',
                'required' => false,
                'input' => 'string',
                'input_format' => 'H:i:s',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'timepicker',
                    'title' => "End timepicker for published",
                ]
            ])
            ->add('onGamersXtv', null, [
                'label' => 'add \'on GamersX TV\' to end of row title'
            ], [
                'type' => 'string'
            ])
            ->add('row_padding_top',null,[
                'label' => 'Row Padding Top(In px)',
            ])
            ->add('row_padding_bottom',null,[
                'label' => 'Row Padding Bottom(In px)',
            ])
        ;
    }
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('title')
            ->add('sortIndex')
            ->add('layout')
            ->add('partner')
            ->add('options')
            ->add('isPublished')
            ->add('isGlowStyling')
            ->add('onGamersXtv')
        ;
    }

    protected function configureBatchActions(array $actions): array
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
            ->add('reorder', $this->getRouterIdParameter() . '/reorder')
            ->add('importForm')
            ->add('import')
        ;
    }

    protected function alterNewInstance(object $object): void
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $roles = $user->getPartnerRoles();

        if (!$roles->isEmpty()) {
            $partner = $roles->first()->getPartner();

            if ($partner !== null) {
                $object->setPartner($partner);
            }
        }
    }
}