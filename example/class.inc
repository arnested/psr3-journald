<?php

namespace Example;

use Psr\Log\LogLevel;
use Psr\Log\LoggerInterface;

class LogFromClass
{
    public function __construct(LoggerInterface $logger)
    {
        $logger->log(LogLevel::DEBUG, 'Hello world from Psr\Log\LoggerInterface::log()');
    }
}
