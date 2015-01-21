<?php

namespace Pinkfire\PinkfireBundle\Monolog\Handler;

use Pinkfire\PinkfireBundle\Service\PinkfireClient;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\RequestStack;

class PinkfireHandler extends AbstractProcessingHandler
{
    protected $requestStack;
    protected $client;

    public function __construct(RequestStack $requestStack, PinkfireClient $client)
    {
        $this->requestStack = $requestStack;
        $this->client = $client;
    }

    protected function write(array $record)
    {
        $path = '';
        $channel = '';
        if ($request = $this->requestStack->getMasterRequest()) {
            $path = $request->attributes->get('_pinkfire_path', '');
            $channel = $request->attributes->get('_pinkfire_channel', '');
        }

        $this->client->push($path, $channel, $record['message'], strtolower($record['level_name']), $record['context']);
    }
}
