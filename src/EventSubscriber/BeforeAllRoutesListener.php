<?php
namespace App\EventSubscriber;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\DBAL\Connection;
use Doctrine\Common\EventManager; // Add this import

class BeforeAllRoutesListener implements EventSubscriberInterface
{
    private $doctrine;

    public function __construct(Connection $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $connection = $this->doctrine;
        $params = $connection->getParams();
        $params['host'] = "172.20.0.4";

        // Create a new EventManager (if needed)
        $eventManager = new EventManager(); 

        // OR, if you have existing listeners you want to keep:
        // $eventManager = $connection->_getEventManager(); // Access the protected property

        $connection->__construct(
            $params,
            $connection->getDriver(),
            $connection->getConfiguration(),
            $eventManager // Pass the EventManager here
        );
    }
}
