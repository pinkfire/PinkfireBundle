<?php

namespace Pinkfire\PinkfireBundle\Guzzle\Subscriber;

use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Event\RequestEvents;
use GuzzleHttp\Event\SubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PinkfireSubscriber implements SubscriberInterface
{
    protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getEvents()
    {
        return [
            'before' => ['onBefore', RequestEvents::EARLY],
        ];
    }

    public function onBefore(BeforeEvent $event)
    {
        $sfRequest = $this->requestStack->getMasterRequest();

        if ($sfRequest && $path = $sfRequest->attributes->get('_pinkfire_path')) {
            $event->getRequest()->setHeader('X-PINKFIRE-PATH', $path);
        }

        if ($sfRequest && $channel = $sfRequest->attributes->get('_pinkfire_channel')) {
            $event->getRequest()->setHeader('X-PINKFIRE-CHANNEL', $channel);
        }
    }
}
