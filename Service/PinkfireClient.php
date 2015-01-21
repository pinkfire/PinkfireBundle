<?php

namespace Pinkfire\PinkfireBundle\Service;

class PinkfireClient
{
    protected $errno;
    protected $errstr;

    protected $application;
    protected $host;
    protected $port;

    public function __construct($application = 'unknown', $host = 'localhost', $port = 3000)
    {
        $this->application = $application;
        $this->host = $host;
        $this->port = $port;
    }

    public function push($path, $channel, $message, $level = 'info', array $context = [])
    {
        $path .= '/'.uniqid();

        try {
            $this->write($this->generateData($path, $channel, $message, $level, $context));
        } catch (\Exception $e) {
        }

        return $path;
    }

    protected function generateData($path, $channel, $message, $level, $context)
    {
        $content = json_encode([
            'application' => $this->application,
            'path' => $path,
            'message' => $message,
            'channel' => $channel,
            'context' => $context,
            'level' => $level,
            'date' => time(),
        ]);

        $header = "POST /threads HTTP/1.0\r\n";
        $header .= "Host: ".$this->host."\r\n";
        $header .= "Content-Type: application/json\r\n";
        $header .= "Content-Length: ".strlen($content)."\r\n";
        $header .= "\r\n";

        return $header.$content;
    }

    protected function write($data)
    {
        $resource = @fsockopen($this->host, $this->port, $this->errno, $this->errstr, 2);
        @fwrite($resource, $data);
        @fclose($resource);
    }
}
