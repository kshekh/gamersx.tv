<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\HomeRow;
use App\Form\SortAndTrimOptionsType;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType, HiddenType, NumberType, TimeType, TimezoneType};
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

final class ContainerAccessControlAdmin extends AbstractAdmin
{
    public function createQuery($context = 'list'): ProxyQuery
    {
        /** @var ProxyQuery $query */
        $query = parent::createQuery($context);
        $rootAlias = $query->getRootAliases()[0];
        $query
            ->setSortOrder('ASC')
            ->setSortBy([], ['fieldName' => 'id']);

        $query->where($rootAlias.'.is_blacklisted = 1');
        $query->orWhere($rootAlias.'.is_full_site_blacklisted = 1');

        return $query;
    }

    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'sortIndex'
    );


    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('create')
            ->remove('delete')
            ->add('remove_blacklisted_container',$this->getRouterIdParameter().'/remove_blacklisted_container')
            ->add('full_site_blacklisted_container',$this->getRouterIdParameter().'/full_site_blacklisted_container');
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('streamer_name')
            ->add('priority')
            ->add('is_blacklist')
            ->add('whitelist')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('home_row_item', null, [
                'label' => 'Row',
                'sortable' => false
            ])
            ->add('game_name', null, [
                'label' => 'Game',
                'sortable' => false
            ])
            ->add('streamer_name', null, [
                'label' => 'Streamer',
                'sortable' => false
            ])
            ->add('priority', null, [
                'sortable' => false,
            ])
            ->add('is_blacklisted', 'string', [
                'label' => 'Blacklist',
                'sortable' => false,
                'template'=> 'ContainerAccessControlAdmin/column_blacklist.html.twig'
            ])
            ->add('is_full_site_blacklisted', 'string', [
                'label' => 'Full site blacklist',
                'sortable' => false,
                'template'=> 'ContainerAccessControlAdmin/column_full_site_blacklist.html.twig'
            ])
        ;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('streamer_name')
        ;
    }
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('streamer_name')
        ;
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