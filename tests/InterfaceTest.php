<?php

declare(strict_types=1);

namespace Arnested\Log;

use Arnested\Log\Journald;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class InterfaceTest extends TestCase
{
    public function testImplementingInterface(): void
    {
        $logger = new Journald();
        $this->assertInstanceOf(LoggerInterface::class, $logger);
    }
}
