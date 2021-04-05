<?php

declare(strict_types=1);

namespace App\Admin;

use App\Form\TwitchType;
use App\Entity\HomeRow;
use App\Service\ThemeImageService;
use Symfony\Component\Form\Extension\Core\Type\{ ChoiceType, FileType };
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class ThemeAdmin extends AbstractAdmin
{
    private $imageSvc;

    public function setThemeImageService(ThemeImageService $service) {
        $this->imageSvc = $service;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('TwitchId')
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
            ->add('TwitchId')
            ->add('label')
            ->add('itemType')
            ->add('bannerImage')
            ->add('artBackground')
            ->add('embedBackground')
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
            ->add('bannerImageFile', FileType::class, [
                'required' => false,
            ])
            ->add('artBackgroundFile', FileType::class, [
                'required' => false,
            ])
            ->add('embedBackgroundFile', FileType::class, [
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
            ->add('TwitchId')
            ->add('label')
            ->add('itemType')
            ->add('bannerImage')
            ->add('artBackground')
            ->add('embedBackground')
            ;
    }

    public function prePersist($theme): void
    {
        $this->imageSvc->setTheme($theme);

        if ($theme->getBannerImageFile()) {
            $theme->setBannerImage(
                $this->imageSvc->upload('banner', $theme->getBannerImageFile())
            );
        }

    }
}
