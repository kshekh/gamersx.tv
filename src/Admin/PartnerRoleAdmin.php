<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\PartnerRole;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class PartnerRoleAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('user')
            ->add('partner')
            ->add('role')
            ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id')
            ->add('user')
            ->add('partner')
            ->add('role')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('user')
            ->add('partner')
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'Admin' => PartnerRole::ADMIN,
                    'Editor' => PartnerRole::EDITOR,
                    'Ad-Only Editor' => PartnerRole::AD_EDITOR,
                ]
            ])
            ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('user')
            ->add('partner')
            ->add('role')
            ;
    }
}
