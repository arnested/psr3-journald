<?php

declare(strict_types=1);

namespace Arnested\Log;

use Arnested\Log\Journald;
use Arnested\Log\Journald\LogLevel as JournaldLogLevel;
use Arnested\Log\Journald\TestSender;
use PHPUnit\Framework\TestCase;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LogLevel;

class LogLevelTest extends TestCase
{
    public function testLogLevelTooLow(): void
    {
        try {
            $sender = new TestSender();
            $logger = new Journald($sender);
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
            $sender = new TestSender();
            $logger = new Journald($sender);
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
            $sender = new TestSender();
            $logger = new Journald($sender);
            $logger->log('foo', 'Foo');

            // An exception _should_ be trown above so fail if we
            // actually reach here.
            $this->fail();
        } catch (\Throwable $t) {
            $this->assertInstanceOf(InvalidArgumentException::class, $t);
        }
    }

    /**
     * @dataProvider provideLogLevelKnownLevel
     */
    public function testLogLevelKnownLevel(string $psr3Level, int $journaldLevel): void
    {
        try {
            $sender = new TestSender();
            $logger = new Journald($sender);
            $logger->log($psr3Level, $psr3Level);
            $this->addToAssertionCount(1);  // does not throw an exception
        } catch (\Throwable $t) {
            $this->assertNotInstanceOf(InvalidArgumentException::class, $t);
        }
    }

    /**
     * @return array<string, array<int|string>>
     */
    public function provideLogLevelKnownLevel(): array
    {
        $map = [];

        foreach (JournaldLogLevel::PSR3_MAP as $psr3Level => $journaldLevel) {
            $map["{$psr3Level} => {$journaldLevel}"] = [
                $psr3Level,
                $journaldLevel,
            ];
        }

        return $map;
    }
}
