<?php

declare(strict_types=1);

namespace Arnested\Log;

use Arnested\Log\Journald;
use PHPUnit\Framework\TestCase;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LogLevel;

class LogLevelTest extends TestCase
{

    public function testLogLevelTooLow(): void
    {
        try {
            $logger = new Journald(['ignore_missing_systemd' => true]);
            $logger->log(-1, 'Foo');

            // An exception _should_ be trown above so fail if we
            // actually reach here.
            $this->fail();
        } catch (\Throwable $t) {
            $this->assertInstanceOf(InvalidArgumentException::class, $t);
        }
    }

    public function testLogLevelTooHigh(): void
    {
        try {
            $logger = new Journald(['ignore_missing_systemd' => true]);
            $logger->log(8, 'Foo');

            // An exception _should_ be trown above so fail if we
            // actually reach here.
            $this->fail();
        } catch (\Throwable $t) {
            $this->assertInstanceOf(InvalidArgumentException::class, $t);
        }
    }

    public function testLogLevelUnknownString(): void
    {
        try {
            $logger = new Journald(['ignore_missing_systemd' => true]);
            $logger->log('foo', 'Foo');

            // An exception _should_ be trown above so fail if we
            // actually reach here.
            $this->fail();
        } catch (\Throwable $t) {
            $this->assertInstanceOf(InvalidArgumentException::class, $t);
        }
    }

    public function testLogLevelKnownString(): void
    {
        try {
            $logger = new Journald(['ignore_missing_systemd' => true]);
            $logger->log(LogLevel::DEBUG, 'Foo');

            $this->assertTrue(true);
        } catch (\Throwable $t) {
            $this->assertNotInstanceOf(InvalidArgumentException::class, $t);
        }
    }
}
