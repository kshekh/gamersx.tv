<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\HomeRow;
use App\Entity\HomeRowItem;
use App\Form\TopicType;
use App\Form\SortAndTrimOptionsType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\{ ChoiceType, HiddenType };
use Symfony\Component\Form\CallbackTransformer;
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
            ->add('topic', null, [
                'sortable' => false,
            ])
            ->add('sortAndTrimOptions', null, [
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
                    'Twitch Link' => HomeRowItem::LINK_TYPE_EXTERNAL,
                ]
            ])
            ->add('homeRow', EntityType::class, [
                'disabled' => true,
                'class' => HomeRow::class,
                'choice_label' => 'title',
                'placeholder' => 'Choose a home row for this item',
                'required' => true,
            ])
            ->add('itemType', ChoiceType::class, [
                'choices' => [
                    'Twitch - Games' => HomeRowItem::TYPE_GAME,
                    'Twitch - Streamers' => HomeRowItem::TYPE_STREAMER,
                    'YouTube - Channels' => HomeRowItem::TYPE_CHANNEL,
                    'YouTube - Popular' => HomeRowItem::TYPE_YOUTUBE,
                ],
                'attr' => [
                    'data-topic-select' => 'itemType',
                ],
                'required' => true
            ])
            ->add('topic', TopicType::class)
            ->add('sortAndTrimOptions', SortAndTrimOptionsType::class, [
                'label' => 'Sort and Trim Options',
                'required' => false,
            ])
            ->getFormBuilder()->addModelTransformer(new CallbackTransformer(
                // Use the array in the form
                function ($valuesAsArray) {
                    return $valuesAsArray;
                },
                // Don't set empty values in the JSON later
                function ($homeRowItem) {
                    $topic = $homeRowItem->getTopic();
                    if (array_key_exists('label', $topic)) {
                        $homeRowItem->setLabel($topic['label']);
                    };

                    return $homeRowItem;
                }
            ))
            ;

    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('sortAndTrimOptions')
            ->add('topic')
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
