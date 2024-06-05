<?php

namespace App\EventSubscriber;

use App\Admin\HomeRowAdmin;
use App\Admin\HomeRowItemAdmin;
use App\Admin\ThemeAdmin;
use Sonata\AdminBundle\Event\PersistenceEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Cache\CacheInterface;

class SonataPersistenceSubscriber implements EventSubscriberInterface
{
    private CacheInterface $gamersxCache;

    public function __construct(CacheInterface $gamersxCache)
    {
        $this->gamersxCache = $gamersxCache;
    }

    public function clearCache(PersistenceEvent $event): void
    {
        $admin = $event->getAdmin();
        if ($admin instanceof HomeRowItemAdmin || $admin instanceof HomeRowAdmin ||
                $admin instanceof ThemeAdmin ) {
            $this->gamersxCache->clear();
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sonata.admin.event.persistence.post_update' => 'clearCache',
            'sonata.admin.event.persistence.post_persist' => 'clearCache',
            'sonata.admin.event.persistence.post_remove' => 'clearCache',
        ];
    }
}
