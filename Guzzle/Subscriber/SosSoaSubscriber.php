<?php

namespace Gloomy\SosSoaBundle\Guzzle\Subscriber;

use GuzzleHttp;
use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Event\RequestEvents;
use GuzzleHttp\Event\SubscriberInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SosSoaSubscriber implements SubscriberInterface
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

        if ($sfRequest && $path = $sfRequest->attributes->get('_sossoa_path')) {
            $event->getRequest()->setHeader('X-SOSSOA-PATH', $path);
        } else {
            $event->getRequest()->setHeader('X-SOSSOA-PATH', '/'.uniqid());
        }
    }
}
