<?php

namespace Gloomy\SosSoaBundle\Monolog\Handler;

use Gloomy\SosSoaBundle\Service\SosSoaClient;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\RequestStack;

class SosSoaHandler extends AbstractProcessingHandler
{
    protected $requestStack;
    protected $client;

    public function __construct(RequestStack $requestStack, SosSoaClient $client)
    {
        $this->requestStack = $requestStack;
        $this->client = $client;
    }

    protected function write(array $record)
    {
        $path = '';
        if ($request = $this->requestStack->getMasterRequest()) {
            $path = $request->attributes->get('_sossoa_path', '');
        }

        $this->client->push($path, $record['message'], strtolower($record['level_name']), $record['context']);
    }
}
