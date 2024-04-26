<?php

declare(strict_types=1);

namespace App\Admin;

use App\Form\TopicType;
use App\Entity\HomeRowItem;
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
            ->add('topicId')
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
            ->add('topicId')
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
                    'Games' => HomeRowItem::TYPE_GAME,
                    'Streamers' => HomeRowItem::TYPE_STREAMER,
                ],
                'attr' => [
                    'data-topic-select' => 'itemType',
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
            ->add('topic', TopicType::class, [
                'searchType' => 'game',
                'inherit_data' => true
            ])
            ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('topicId')
            ->add('label')
            ->add('itemType')
            ->add('bannerImage')
            ->add('embedBackground')
            ->add('customArt')
            ->add('artBackground')
            ;
    }
}
