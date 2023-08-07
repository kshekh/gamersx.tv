<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\SiteSettings;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;

final class SiteSettingsAdmin extends AbstractAdmin
{
    /**
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $row = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager()->getRepository(SiteSettings::class)->findOneBy([]);
        $collection->add('save_theme_setting','save_theme_setting');
        $collection->remove('export');

        if (isset($row) && $row->getId() == true) {
            $collection->remove('create');
        }
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper): void
    {
        if ($this->isGranted('ROLE_LOGIN_ALLOWED')) {
            $listMapper
                ->add('disableHomeAccess', null, [
                    'editable' => true,
                    'label' => 'Disable HomePage Access'
                ])
            ;
        }
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        if ($this->isGranted('ROLE_LOGIN_ALLOWED')) {
            $formMapper
                ->add('disableHomeAccess', null, [
                    'label' => 'Disable HomePage Access'
                ])
            ;
        }
    }

    protected function configureBatchActions($actions)
    {
        unset($actions['export']);
        unset($actions['delete']);

        return $actions;
    }

    public function configureActionButtons($action, $object = null)
    {
        $buttons = parent::configureActionButtons($action, $object);
        if (in_array($action, array('list'))) {
            unset($buttons['create']);
        }

        return $buttons;
    }
}
