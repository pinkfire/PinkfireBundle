<?php

namespace Pinkfire\PinkfireBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class RequestAwareClient
{
    protected $requestStack;
    protected $client;

    public function __construct(RequestStack $requestStack, PinkfireClient $client)
    {
        $this->requestStack = $requestStack;
        $this->client = $client;
    }

    public function push($message, $level = 'info', array $context = [], array $links = [])
    {
        return $this->client->push($this->getPath(), $this->getChannel(), $message, $level, $context, $links);
    }

    public function patch($message, $level = 'info', array $context = [], array $links = [])
    {
        $this->client->patch($this->getPath(), $this->getChannel(), $message, $level, $context, $links);
    }

    private function getPath()
    {
        if ($master = $this->requestStack->getMasterRequest()) {
            return $master->attributes->get('_pinkfire_path', '');
        }

        return '';
    }

    private function getChannel()
    {
        if ($master = $this->requestStack->getMasterRequest()) {
            return $master->attributes->get('_pinkfire_channel', '');
        }

        return '';
    }
}
