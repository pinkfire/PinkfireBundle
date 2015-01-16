<?php

namespace Gloomy\SosSoaBundle\EventListener;

use Gloomy\SosSoaBundle\Service\SosSoaClient;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
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

        $path = $this->client->push($parent, sprintf('New master request "%s" (%s)', $request->getPathInfo(), $request->getMethod()), 'primary', $this->getContext($request));
        $request->attributes->set('_sossoa_path', $path);
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
