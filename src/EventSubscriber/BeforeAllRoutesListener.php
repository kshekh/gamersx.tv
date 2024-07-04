<?php
namespace App\EventSubscriber;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Connection;

class BeforeAllRoutesListener implements EventSubscriberInterface
{
    private $doctrine;

    public function __construct(Connection $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public static function getSubscribedEvents()
    {
        // Register the method to be called on the kernel.request event
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        // Check if this is the master request (not a sub-request)
        // if (!$event->isMainRequest()) {
        //     dd("dadi");
        //     return;
        // }

        $connection = $this->doctrine;
        $params = $connection->getParams();
        $params['host'] = "172.20.0.4";
        $connection->__construct(
            $params,
            $connection->getDriver(),
            $connection->getConfiguration(),
            $connection->getEventManager()
        );
        // dd($connection->getParams());
    }
}
