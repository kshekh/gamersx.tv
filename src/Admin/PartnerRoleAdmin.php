<?php

namespace App\Admin;

use App\Entity\Partner;
use App\Entity\PartnerRole;
use App\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PartnerRoleAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
            ])
            ->add('partner', ModelType::class, [
                'class' => Partner::class,
                'property' => 'name',
            ])
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'Admin' => PartnerRole::ADMIN,
                    'Editor' => PartnerRole::EDITOR,
                    'Ad-Only Editor' => PartnerRole::AD_EDITOR,
                ]
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('id')
            ->add('user')
            ->add('partner')
            ->add('role');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id')
            ->add('user.username')
            ->add('partner.name')
            ->add('role')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('user.username')
            ->add('partner.name')
            ->add('role');
    }
}