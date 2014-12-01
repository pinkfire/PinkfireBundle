<?php

namespace Gloomy\SosSoaBundle\Service;

class SosSoaClient
{
    protected $errno;
    protected $errstr;

    protected $application;
    protected $host;

    public function __construct($application = 'unknown', $host = 'tcp://localhost:3000')
    {
        $this->application = $application;
        $this->host = $host;
    }

    public function push($path, $message, $level = 'info', array $context = [])
    {
        $path .= '/'.uniqid();

        try {
            $this->write($this->generateData($path, $message, $level, $context));
        } catch (\Exception $e) {
        }

        return $path;
    }

    protected function generateData($path, $message, $level, $context)
    {
        $content = json_encode([
            'application' => $this->application,
            'path' => $path,
            'message' => $message,
            'context' => $context,
            'level' => $level
        ]);

        $header = "POST /threads HTTP/1.0\r\n";
        $header .= "Content-Type: application/json\r\n";
        $header .= "Content-Length: " . strlen($content) . "\r\n";
        $header .= "\r\n";

        return $header.$content;
    }

    protected function write($data)
    {
        $resource = @fsockopen($this->host, -1, $this->errno, $this->errstr, 2);
        @fwrite($resource, $data);
        @fclose($resource);
    }
}
