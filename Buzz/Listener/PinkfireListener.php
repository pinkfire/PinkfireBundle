<?php

namespace Pinkfire\PinkfireBundle\Buzz\Listener;

use Buzz\Listener\ListenerInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PinkfireListener implements ListenerInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function preSend(RequestInterface $request)
    {
        $sfRequest = $this->requestStack->getMasterRequest();

        if ($sfRequest && $path = $sfRequest->attributes->get('_pinkfire_path')) {
            $request->addHeaders(['X-PINKFIRE-PATH' => $path]);
        }

        if ($sfRequest && $channel = $sfRequest->attributes->get('_pinkfire_channel')) {
            $request->addHeaders(['X-PINKFIRE-CHANNEL' => $channel]);
        }
    }

    public function postSend(RequestInterface $request, MessageInterface $response)
    {
        // do nothing
    }
}
