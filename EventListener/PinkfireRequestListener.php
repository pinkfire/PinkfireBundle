<?php

namespace Pinkfire\PinkfireBundle\EventListener;

use Pinkfire\PinkfireBundle\Service\RequestAwareClient;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class PinkfireRequestListener implements EventSubscriberInterface
{
    const IN = "\xE2\x86\x98 ";
    const OUT = "\xE2\xAC\x85 ";
    const HIDDEN = '_pinkfire_';

    protected $client;
    protected $urlBlacklist;
    protected $urlDebuglist;

    public function __construct(RequestAwareClient $client, array $urlBlacklist = [], array $urlDebuglist = [])
    {
        $this->client = $client;
        $this->urlBlacklist = $urlBlacklist;
        $this->urlDebuglist = $urlDebuglist;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        if ($this->isBlacklisted($request)) {
            return;
        }

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

        if ($this->isBlacklisted($request)) {
            return;
        }

        $links = [];
        if ($response->headers->has('X-Debug-Token-Link')) {
            $links = ['Profiler' => $request->getSchemeAndHttpHost().$response->headers->get('X-Debug-Token-Link')];
        }

        $this->client->patch(null, null, $this->getResponseContext($response), $links);
    }

    protected function getRequestContext(Request $request)
    {
        return [
            self::HIDDEN.'is_debug' => $this->isDebug($request),
            self::IN.'_uri' => $request->getRequestUri(),
            self::IN.'query' => $request->query->all(),
            self::IN.'request' => $request->request->all(),
            self::IN.'headers' => $request->headers->all(),
            self::IN.'server' => $request->server->all(),
            self::IN.'files' => $request->files->all(),
            self::IN.'cookies' => $request->cookies->all(),
            self::IN.'attributes' => $request->attributes->all(),
        ];
    }

    protected function getResponseContext(Response $response)
    {
        return [
            self::OUT.'body' => $response->getContent(),
            self::OUT.'headers' => $response->headers->all(),
        ];
    }

    protected function isBlacklisted(Request $request)
    {
        foreach ($this->urlBlacklist as $pattern) {
            if (preg_match(sprintf('#^/%s$#', $pattern), $request->getPathInfo())) {
                return true;
            }
        }

        return false;
    }

    protected function isDebug(Request $request)
    {
        foreach ($this->urlDebuglist as $pattern) {
            if (preg_match(sprintf('#^/%s$#', $pattern), $request->getPathInfo())) {
                return true;
            }
        }

        return false;
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest', 255),
            KernelEvents::RESPONSE => array('onKernelResponse', -255),
        );
    }
}
