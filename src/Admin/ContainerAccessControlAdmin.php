<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ContainerAccessControlAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form->add('streamer_name');
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('streamer_name')
            ->add('priority')
            ->add('is_blacklist')
            ->add('whitelist');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('home_row_item', null, [
                'label' => 'Row',
                'sortable' => false
            ])
            ->add('game_name', null, [
                'label' => 'Game',
                'sortable' => false
            ])
            ->add('streamer_name', null, [
                'label' => 'Streamer',
                'sortable' => false
            ])
            ->add('priority', null, [
                'sortable' => false,
            ])
            ->add('is_blacklisted', 'string', [
                'label' => 'Blacklist',
                'sortable' => false,
                'template'=> 'ContainerAccessControlAdmin/column_blacklist.html.twig'
            ])
            ->add('is_full_site_blacklisted', 'string', [
                'label' => 'Full site blacklist',
                'sortable' => false,
                'template'=> 'ContainerAccessControlAdmin/column_full_site_blacklist.html.twig'
            ]);
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('streamer_name');
    }
}