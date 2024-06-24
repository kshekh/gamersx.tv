<?php

namespace App\Admin;

use App\Entity\SiteSettings;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;

class SiteSettingsAdmin extends AbstractAdmin
{
//    private EntityManagerInterface $entityManager;
//
//    public function __construct(EntityManagerInterface $entityManager)
//    {
//        $this->entityManager = $entityManager;
//    }

    /**
     * @throws \JsonException
     */
    protected function configureFormFields(FormMapper $form): void
    {
        if ($this->isGranted('ROLE_LOGIN_ALLOWED')) {
            $form
                ->add('disableHomeAccess', null, [
                    'label' => 'Disable HomePage Access'
                ])
            ;
        }
    }

    /**
     * @throws \JsonException
     */
    protected function configureListFields(ListMapper $list): void
    {
        if ($this->isGranted('ROLE_LOGIN_ALLOWED')) {
            $list
                ->add('disableHomeAccess', null, [
                    'editable' => true,
                    'label' => 'Disable HomePage Access'
                ])
            ;
        }
    }

//    protected function configureRoutes(RouteCollectionInterface $collection): void
//    {
//        $row = $this->entityManager->getRepository(SiteSettings::class)->findOneBy([]);
//        $collection->add('save_theme_setting','save_theme_setting');
//        $collection->add('save_theme','save_theme');
//        $collection->add('get_theme_setting','get_theme_setting');
//        $collection->remove('export');
//
//        if (isset($row) && $row->getId() == true) {
//            $collection->remove('create');
//        }
//    }
}