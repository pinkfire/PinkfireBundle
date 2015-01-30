<?php

namespace Pinkfire\PinkfireBundle\EventListener;

use Pinkfire\PinkfireBundle\Service\RequestAwareClient;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class PinkfireRequestListener implements EventSubscriberInterface
{
    protected $client;

    public function __construct(RequestAwareClient $client)
    {
        $this->client = $client;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        $request->attributes->set('_pinkfire_channel', $request->headers->get('X-PINKFIRE-CHANNEL', ''));
        $request->attributes->set('_pinkfire_path', $request->headers->get('X-PINKFIRE-PATH', ''));

        $path = $this->client->push(sprintf('New master request "%s" (%s)', $request->getPathInfo(), $request->getMethod()), 'primary', $this->getRequestContext($request));

        $request->attributes->set('_pinkfire_path', $path);
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();

        $context = ['body' => $response->getContent()];

        $links = [];
        if ($response->headers->has('X-Debug-Token-Link')) {
            $links = ['Profiler' => 'http://' . $request->getHost() . $response->headers->get('X-Debug-Token-Link')];
        }

        $this->client->patch(null, null, $context, $links);
    }

    protected function getRequestContext(Request $request)
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
            KernelEvents::RESPONSE => array('onKernelResponse', -255),
        );
    }
}
