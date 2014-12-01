<?php

namespace Gloomy\SosSoaBundle\EventListener;

use Gloomy\SosSoaBundle\Service\SosSoaClient;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\KernelEvents;

class SosSoaRequestListener implements EventSubscriberInterface
{
    protected $client;

    public function __construct(SosSoaClient $client)
    {
        $this->client = $client;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernel::MASTER_REQUEST != $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();
        $parent = $request->headers->get('X-SOSSOA-PATH', '');

        $path = $this->client->push($parent, sprintf('New master request "%s" (%s)', $request->getPathInfo(), $request->getMethod()), 'primary');
        $request->attributes->set('_sossoa_path', $path);
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest', 255),
        );
    }
}
