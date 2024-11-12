<?php

declare(strict_types=1);

namespace Arnested\Log;

use Arnested\Log\Journald;
use Arnested\Log\Journald\Sender;
use Arnested\Log\Journald\TestSender;
use FFI\Exception as FFIException;
use PHPUnit\Framework\TestCase;

class SystemdSharedObjectTest extends TestCase
{
    public function testInvalidOrMisisingSystemdSharedObject(): void
    {
        try {
            $sender = new Sender('foo bar baz.so.0');
            new Journald($sender);

            // An exception _should_ be trown above so fail if we
            // actually reach here.
            $this->fail();
        } catch (\Throwable $t) {
            $this->assertInstanceOf(FFIException::class, $t);
        }
    }

    public function testIgnoreFfiException(): void
    {
        try {
            $sender = new TestSender();
            new Journald($sender);
            $this->addToAssertionCount(1);  // does not throw an exception
        } catch (\Throwable $t) {
            $this->assertNotInstanceOf(FFIException::class, $t);
        }
    }
}
