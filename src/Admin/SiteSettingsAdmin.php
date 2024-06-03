<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\SiteSettings;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class SiteSettingsAdmin extends AbstractAdmin
{
    public EntityManagerInterface $entityManager;

    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $row = $this->entityManager->getRepository(SiteSettings::class)->findOneBy([]);
        $collection->add('save_theme_setting','save_theme_setting');
        $collection->add('save_theme','save_theme');
        $collection->add('get_theme_setting','get_theme_setting');
        $collection->remove('export');

        if (isset($row) && $row->getId()) {
            $collection->remove('create');
        }
    }

    /**
     * @param ListMapper $list
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

    protected function configureBatchActions(array $actions): array
    {
        unset($actions['export']);
        unset($actions['delete']);

        return $actions;
    }

    public function configureActionButtons(array $buttonList, string $action, ?object $object = null): array
    {
        $buttons = parent::configureActionButtons($buttonList, $action, $object);
        if (in_array($action, array('list'))) {
            unset($buttons['create']);
        }

        return $buttons;
    }
}
