<?php

declare(strict_types=1);

namespace App\Admin;

use App\Form\TwitchType;
use App\Entity\HomeRow;
use Symfony\Component\Form\Extension\Core\Type\{ ChoiceType, FileType };
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

final class ThemeAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('TwitchId')
            ->add('label')
            ->add('itemType')
            ->add('hasBannerImage')
            ->add('hasArtBackground')
            ->add('hasEmbedBackground')
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('TwitchId')
            ->add('label')
            ->add('itemType')
            ->add('hasBannerImage')
            ->add('hasArtBackground')
            ->add('hasEmbedBackground')
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
            ->add('itemType', ChoiceType::class, [
                'choices' => [
                    'Games' => HomeRow::ITEM_TYPE_GAME,
                    'Streamers' => HomeRow::ITEM_TYPE_STREAMER,
                ],
                'required' => true
            ])
            ->add('bannerImage', FileType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('artBackground', FileType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('embedBackground', FileType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('TwitchId', TwitchType::class, [
                'searchType' => 'game',
            ])
            ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('TwitchId')
            ->add('label')
            ->add('itemType')
            ->add('hasBannerImage')
            ->add('hasArtBackground')
            ->add('hasEmbedBackground')
            ;
    }
}
