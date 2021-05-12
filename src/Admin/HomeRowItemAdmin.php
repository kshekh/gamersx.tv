<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\HomeRow;
use App\Entity\HomeRowItem;
use App\Form\RowOptionsType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

final class HomeRowItemAdmin extends AbstractAdmin
{

    public function createQuery($context = 'list'): ProxyQuery
    {
        /** @var ProxyQuery $query */
        $query = parent::createQuery($context);

        return $query
            ->setSortOrder('ASC')
            ->setSortBy([], ['fieldName' => 'sortIndex'])
            ;
    }

    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'sortIndex'
    );

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('twitchId')
            ->add('label')
            ->add('itemType')
            ->add('homeRow')
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('homeRow', null, [
                'sortable' => false,
            ])
            ->add('label', null, [
                'sortable' => false,
            ])
            ->add('itemType', null, [
                'sortable' => false
            ])
            ->add('containerizerOptions', null, [
                'sortable' => false,
            ])
            ->add('sortIndex', null, [
                'editable' => TRUE,
                'sortable' => false,
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

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('sortIndex')
            ->add('itemType', ChoiceType::class, [
                'choices' => [
                    'Games' => HomeRowItem::TYPE_GAME,
                    'Streamers' => HomeRowItem::TYPE_STREAMER,
                    'Channels' => HomeRowItem::TYPE_CHANNEL,
                    'YouTube' => HomeRowItem::TYPE_YOUTUBE,
                    'Popular' => HomeRowItem::TYPE_POPULAR,
                ]
            ])
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
            ->add('containerizerOptions', RowOptionsType::class)
            ;

    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('containerizerOptions')
            ->add('label')
            ->add('itemType')
            ->add('sortIndex')
            ->add('showArt')
            ->add('offlineDisplayType')
            ->add('linkType')
            ;
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->add('reorder', $this->getRouterIdParameter().'/reorder');
    }

}
