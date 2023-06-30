<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\HomeRow;
use App\Entity\HomeRowItem;
use App\Form\TopicType;
use App\Form\SortAndTrimOptionsType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType, HiddenType, TimeType};
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\CallbackTransformer;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class HomeRowItemAdmin extends AbstractAdmin
{

    public function createQuery($context = 'list'): ProxyQuery
    {
        /** @var ProxyQuery $query */
        $query = parent::createQuery($context);
        $rootAlias = $query->getRootAliases()[0];

        $query
            ->setSortOrder('ASC')
            ->setSortBy([], ['fieldName' => 'sortIndex']);

        $query->where($query->expr()->andX(
            $query->expr()->eq($rootAlias.'.itemType', ':itemType'),
            $query->expr()->eq($rootAlias.'.playlistId', ':playlistId')
        ));
        $query->orWhere($rootAlias.'.itemType != :itemType');
        $query->setParameter(':itemType', 'youtube_video');
        $query->setParameter(':playlistId', '');

        return $query;
    }

    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'sortIndex'
    );

    public function configureActionButtons($action, $object = null)
    {
        $list = parent::configureActionButtons($action, $object);
        $list['importForm']['template'] = 'CRUD/import_button.html.twig';
        return $list;
    }

    public function getDashboardActions()
    {
        $actions = parent::getDashboardActions();

        $actions['importForm'] = [
            'label' => 'Import',
            'url' => $this->generateUrl('importForm'),
            'icon' => 'level-up',
        ];

        return $actions;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('label')
            ->add('itemType')
            ->add('videoId')
            ->add('playlistId')
            ->add('partner')
            ->add('homeRow')
            ->add('isPublished')
            ->add('isPartner');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $homeRows = $this->getModelManager()->findBy(HomeRow::class);
        $homeRowsObjects = [];
        $homeRowsTitles = [];
        $homeRowsIds = [];

        foreach ($homeRows as $homeRow) {
            $homeRowsObjects[$homeRow->getId()] = $homeRow;
            $homeRowsTitles[$homeRow->getId()] = $homeRow->getTitle();
            $homeRowsIds[$homeRow->getId()] = $homeRow->getId();
        }


        $listMapper
            ->add('homeRow', 'choice', [
                'editable' => true,
                'class' => HomeRow::class,
                'choices' => $homeRowsTitles,
                'sortable' => false,
            ])
            ->add('label', null, [
                'sortable' => false,
            ])
            ->add('itemType', null, [
                'sortable' => false
            ])
            ->add('videoId', null, [
                'sortable' => false
            ])
            ->add('playlistId', null, [
                'sortable' => false
            ])
            ->add('topic', null, [
                'sortable' => false,
            ])
            ->add('sortAndTrimOptions', null, [
                'sortable' => false,
            ])
            ->add('sortIndex', null, [
                'editable' => true,
                'sortable' => false
            ])
            ->add('partner')
            ->add('isPublished', null, [
                'editable' => true,
                'sortable' => false
            ])
            ->add('isPartner', null, [
                'sortable' => false
            ])
            ->add('isPublishedStart', null, [
                'editable' => true,
                'sortable' => false
            ])
            ->add('isPublishedEnd', null, [
                'editable' => true,
                'sortable' => false
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
            ->add('showArt', null, [
                'label' => 'Show API Thumbnail'
            ])
            ->add('customArtFile', VichImageType::class, [
                'required' => false,
            ])
            ->add('overlayArtFile', VichImageType::class, [
                'required' => false,
            ])
            ->add('offlineDisplayType', ChoiceType::class, [
                'choices' => [
                    'API Source Thumbnail' => HomeRowItem::OFFLINE_DISPLAY_ART,
                    'Overlay' => HomeRowItem::OFFLINE_DISPLAY_OVERLAY,
                    'Offline Stream Embed' => HomeRowItem::OFFLINE_DISPLAY_STREAM,
                    'Don\'t Show' => HomeRowItem::OFFLINE_DISPLAY_NONE,
                ]
            ])
            ->add('linkType', ChoiceType::class, [
                'choices' => [
                    'GamersX Link' => HomeRowItem::LINK_TYPE_GAMERSX,
                    'External Link' => HomeRowItem::LINK_TYPE_EXTERNAL,
                    'Custom Link' => HomeRowItem::LINK_TYPE_CUSTOM,
                ]
            ])
            ->add('customLink', null, [
                'required' => false,
            ])
            ->add('partner')
            ->add('homeRow', EntityType::class, [
                'class' => HomeRow::class,
                'choice_label' => 'title',
                'placeholder' => 'Choose a home row for this item',
                'required' => true,
                'attr' => [
                    'oninvalid' => "alert('Please choose a Home Row')",
                ],
            ])
            ->add('itemType', ChoiceType::class, [
                'choices' => [
                    'Twitch - Game' => HomeRowItem::TYPE_GAME,
                    'Twitch - Streamer' => HomeRowItem::TYPE_STREAMER,
                    'YouTube - Channel' => HomeRowItem::TYPE_CHANNEL,
                    'YouTube - Query' => HomeRowItem::TYPE_YOUTUBE,
                    'No Embed - Link Only' => HomeRowItem::TYPE_LINK,
                    'Twitch - Video' => HomeRowItem::TYPE_TWITCH_VIDEO,
                    'Twitch - Playlist' => HomeRowItem::TYPE_TWITCH_PLAYLIST,
                    'YouTube - Video' => HomeRowItem::TYPE_YOUTUBE_VIDEO,
                    'YouTube - Playlist' => HomeRowItem::TYPE_YOUTUBE_PLAYLIST,
                ],
                'attr' => [
                    'data-topic-select' => 'itemType',
                    'class' => 'container-item-type'
                ],
                'required' => true
            ])
            ->add('videoId', null, [
                'required' => false,
                'label' => 'Video ID',
                'attr' => [
                    'class' => 'video-id'
                ]
            ])
            ->add('playlistId', null, [
                'required' => false,
                'label' => 'Playlist ID',
                'attr' => [
                    'class' => 'playlist-id'
                ]
            ])
            ->add('topic', TopicType::class, [
                'required' => false,
            ])
            ->add('sortAndTrimOptions', SortAndTrimOptionsType::class, [
                'label' => 'Sort and Trim Options',
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('isPublished', null, [
                'help' => 'Current Server Time: ' . date('H:i')
            ])
            ->add('isPublishedStart', TimeType::class, [
                'label' => 'Publish Start Time',
                'required' => false,
                'input'  => 'timestamp',
                'widget' => 'single_text',
                'model_timezone' => 'America/Los_Angeles',
                'view_timezone' => 'UTC',
                'attr' => [
                    'class' => 'timepicker',
                    'title' => "Start timepicker for published",
                ]
            ])
            ->add('isPublishedEnd', TimeType::class, [
                'label' => 'Publish End Time',
                'required' => false,
                'input'  => 'timestamp',
                'widget' => 'single_text',
                'model_timezone' => 'America/Los_Angeles',
                'view_timezone' => 'UTC',
                'attr' => [
                    'class' => 'timepicker',
                    'title' => "End timepicker for published",
                ]
            ])
            ->add('isPartner')
            ->getFormBuilder()->addModelTransformer(new CallbackTransformer(
                // Use the array in the form
                function ($valuesAsArray) {
                    return $valuesAsArray;
                },
                // Don't set empty values in the JSON later
                function ($homeRowItem) {
                    $topic = $homeRowItem->getTopic();
                    if ($topic && array_key_exists('label', $topic)) {
                        $homeRowItem->setLabel($topic['label']);
                    };

                    return $homeRowItem;
                }
            ));
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('sortAndTrimOptions')
            ->add('topic')
            ->add('label')
            ->add('itemType')
            ->add('videoId')
            ->add('playlistId')
            ->add('sortIndex')
            ->add('showArt')
            ->add('customArt')
            ->add('overlayArt')
            ->add('offlineDisplayType')
            ->add('linkType')
            ->add('partner')
            ->add('description')
            ->add('isPublished')
            ->add('isPartner');
    }

    protected function configureBatchActions($actions)
    {

        if ($this->hasRoute('list') && $this->hasAccess('list')) {
            $actions['export'] = [
                'label' => 'Download Export Zip',
                'ask_confirmation' => FALSE,
            ];
        }

        return $actions;
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->add('reorder', $this->getRouterIdParameter() . '/reorder')
            ->add('importForm')
            ->add('import');
    }

    public function alterNewInstance(object $instance): void
    {
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $roles = $user->getPartnerRoles();

        if (!$roles->isEmpty()) {
            $partner = $roles->first()->getPartner();

            if ($partner !== null) {
                $instance->setPartner($partner);
            }
        }
    }
}
