<?php

declare(strict_types=1);

namespace App\Admin;

use App\Form\TwitchType;
use App\Entity\HomeRow;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            ->add('embedBackground')
            ->add('customArt')
            ->add('artBackground')
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('twitchId')
            ->add('label')
            ->add('itemType')
            ->add('bannerImage')
            ->add('embedBackground')
            ->add('customArt')
            ->add('artBackground')
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
                'attr' => [
                    'data-twitch-select' => 'itemType',
                ],
                'required' => true
            ])
            ->add('bannerImageFile', VichImageType::class, [
                'required' => false,
            ])
            ->add('embedBackgroundFile', VichImageType::class, [
                'required' => false,
            ])
            ->add('customArtFile', VichImageType::class, [
                'required' => false,
            ])
            ->add('artBackgroundFile', VichImageType::class, [
                'required' => false,
            ])
            ->add('twitch', TwitchType::class, [
                'searchType' => 'game',
                'inherit_data' => true
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
            ->add('embedBackground')
            ->add('customArt')
            ->add('artBackground')
            ;
    }
}
