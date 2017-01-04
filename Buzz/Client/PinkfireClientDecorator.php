<?php

namespace Pinkfire\PinkfireBundle\Buzz\Client;

use Buzz\Client\ClientInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use Pinkfire\PinkfireBundle\Buzz\Listener\PinkfireListener;

class PinkfireClientDecorator implements ClientInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var PinkfireListener
     */
    private $listener;

    public function __construct(ClientInterface $client, PinkfireListener $listener)
    {
        $this->client = $client;
        $this->listener = $listener;
    }


    public function send(RequestInterface $request, MessageInterface $response)
    {
        $this->listener->preSend($request);
        $this->client->send($request, $response);
    }
}
