<?php

namespace App\Admin;

use App\Form\SortAndTrimOptionsType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;

class HomeRowAdmin extends AbstractAdmin
{
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
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('title')
            ->add('partner')
            ->add('isPublished')
            ->add('isGlowStyling')
            ->add('onGamersXtv');
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
            ]);
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
            ->add('onGamersXtv');
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
}