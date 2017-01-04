<?php

namespace Pinkfire\PinkfireBundle\Guzzle\Middleware;

use Psr\Http\Message\RequestInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PinkfireMiddleware
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function __invoke(callable $nextHandler)
    {
        return function (RequestInterface $request, array $options) use ($nextHandler) {
            $sfRequest = $this->requestStack->getMasterRequest();

            if ($sfRequest && $path = $sfRequest->attributes->get('_pinkfire_path')) {
                $request = $request->withHeader('X-PINKFIRE-PATH', $path);
            }

            if ($sfRequest && $channel = $sfRequest->attributes->get('_pinkfire_channel')) {
                $request = $request->withHeader('X-PINKFIRE-CHANNEL', $channel);
            }

            return $nextHandler($request, $options);
        };
    }
}
