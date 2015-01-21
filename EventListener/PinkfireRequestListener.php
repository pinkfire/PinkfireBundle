<?php

namespace Pinkfire\PinkfireBundle\EventListener;

use Pinkfire\PinkfireBundle\Service\PinkfireClient;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\KernelEvents;

class PinkfireRequestListener implements EventSubscriberInterface
{
    protected $client;

    public function __construct(PinkfireClient $client)
    {
        $this->client = $client;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernel::MASTER_REQUEST != $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();
        $parent = $request->headers->get('X-PINKFIRE-PATH', '');
        $channel = $request->headers->get('X-PINKFIRE-CHANNEL', '');

        $path = $this->client->push($parent, $channel, sprintf('New master request "%s" (%s)', $request->getPathInfo(), $request->getMethod()), 'primary', $this->getContext($request));
        $request->attributes->set('_pinkfire_path', $path);
        $request->attributes->set('_pinkfire_channel', $channel);
    }

    protected function getContext(Request $request)
    {
        return [
            'query' => $request->query->all(),
            'request' => $request->request->all(),
            'headers' => $request->headers->all(),
            'server' => $request->server->all(),
            'files' => $request->files->all(),
            'cookies' => $request->cookies->all(),
            'attributes' => $request->attributes->all(),
        ];
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest', 255),
        );
    }
}
