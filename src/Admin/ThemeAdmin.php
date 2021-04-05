<?php

declare(strict_types=1);

namespace App\Admin;

use App\Form\TwitchType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class ThemeAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('twitchId')
            ->add('label')
            ->add('itemType')
            ->add('bannerImage')
            ->add('artBackground')
            ->add('embedBackground')
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('twitchId')
            ->add('label')
            ->add('itemType')
            ->add('bannerImage')
            ->add('artBackground')
            ->add('embedBackground')
            ->add('_action', null, [
            ->add(ListMapper::NAME_ACTIONS, null, [
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
            ->add('itemType', ChoiceType::class, [
                'choices' => [
                    'Games' => HomeRow::ITEM_TYPE_GAME,
                    'Streamers' => HomeRow::ITEM_TYPE_STREAMER,
                ],
                'required' => true
            ])
            ->add('bannerImageFile', VichImageType::class)
            ->add('embedBackgroundFile', VichImageType::class)
            ->add('artBackgroundFile', VichImageType::class)
            ->add('twitch', TwitchType::class, [
                'searchType' => 'game',
                'inherit_data' => true
            ->add('itemType')
            ->add('twitch', TwitchType::class, [
                'inherit_data' => true,
                'searchType' => 'game',
            ])
            ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('twitchId')
            ->add('label')
            ->add('itemType')
            ->add('bannerImage')
            ->add('artBackground')
            ->add('embedBackground')
            ;
    }
}
