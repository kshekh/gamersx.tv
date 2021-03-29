<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\HomeRow;
use App\Entity\HomeRowItem;
use App\Form\TwitchType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;

final class HomeRowItemAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('twitchId')
            ->add('label')
            ->add('homeRow')
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('homeRow')
            ->add('label')
            ->add('twitchId')
            ->add('sortIndex', null, [
                'editable' => TRUE,
            ])
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
            ->add('sortIndex')
            ->add('showArt')
            ->add('offlineDisplayType', ChoiceType::class, [
                'choices' => [
                    'Thumbnail' => HomeRowItem::OFFLINE_DISPLAY_ART,
                    'Offline Stream Embed' => HomeRowItem::OFFLINE_DISPLAY_STREAM,
                    'Don\'t Show' => HomeRowItem::OFFLINE_DISPLAY_NONE,
                ]
            ])
            ->add('linkType', ChoiceType::class, [
                'choices' => [
                    'GamersX Link' => HomeRowItem::LINK_TYPE_GAMERSX,
                    'Twitch Link' => HomeRowItem::LINK_TYPE_TWITCH,
                ]
            ])
            ->add('homeRow', EntityType::class, [
                'disabled' => true,
                'class' => HomeRow::class,
                'choice_label' => 'title',
                'placeholder' => 'Choose a home row for this item',
                'required' => true,
            ])
            ->add('twitch', TwitchType::class)
            ;

    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('twitchId')
            ->add('label')
            ->add('sortIndex')
            ->add('showArt')
            ->add('offlineDisplayType')
            ->add('linkType')
            ;
    }

}
