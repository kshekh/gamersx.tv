<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class ContainerAccessControlAdmin extends AbstractAdmin
{
    private $tokenStorage;

    public function setTokenStorage(TokenStorageInterface $tokenStorage): void
    {
        $this->tokenStorage = $tokenStorage;
    }

//    public function createQuery($context = 'list'): ProxyQuery
//    {
//        /** @var ProxyQuery $query */
//        $query = parent::createQuery($context);
//        $rootAlias = $query->getRootAliases()[0];
//        $query
//            ->setSortOrder('ASC')
//            ->setSortBy([], ['fieldName' => 'id']);
//
//        $query->where($rootAlias.'.is_blacklisted = 1');
//        $query->orWhere($rootAlias.'.is_full_site_blacklisted = 1');
//
//        return $query;
//    }

    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $query = parent::configureQuery($query);
        $rootAlias = current($query->getRootAliases());

        $query->where($rootAlias.'.is_blacklisted = 1');
        $query->orWhere($rootAlias.'.is_full_site_blacklisted = 1');

        return $query;
    }

//    protected $datagridValues = array(
//        '_page' => 1,
//        '_sort_order' => 'ASC',
//        '_sort_by' => 'sortIndex'
//    );

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues[DatagridInterface::PAGE] = 1;
        $sortValues[DatagridInterface::SORT_ORDER] = 'ASC';
        $sortValues[DatagridInterface::SORT_BY] = 'sortIndex';
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('create')
            ->remove('delete')
            ->add('remove_blacklisted_container',$this->getRouterIdParameter().'/remove_blacklisted_container')
            ->add('full_site_blacklisted_container',$this->getRouterIdParameter().'/full_site_blacklisted_container');
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

    protected function configureBatchActions(array $actions): array
    {

        if ($this->hasRoute('list') && $this->hasAccess('list')) {
            $actions['export'] = [
                'label' => 'Download Export Zip',
                'ask_confirmation' => FALSE,
            ];
        }

        return $actions;
    }


    public function alterNewInstance(object $object): void
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $roles = $user->getPartnerRoles();

        if (!$roles->isEmpty()) {
            $partner = $roles->first()->getPartner();

            if ($partner !== null) {
                $object->setPartner($partner);
            }
        }
    }
}