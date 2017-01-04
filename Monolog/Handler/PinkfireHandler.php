<?php

namespace Pinkfire\PinkfireBundle\Monolog\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Pinkfire\PinkfireBundle\Service\RequestAwareClient;

class PinkfireHandler extends AbstractProcessingHandler
{
    protected $client;

    public function __construct(RequestAwareClient $client, $level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->client = $client;
    }

    protected function write(array $record)
    {
        $this->client->push($record['message'], strtolower($record['level_name']), $record['context']);
    }
}
