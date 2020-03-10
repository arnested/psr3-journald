<?php

declare(strict_types=1);

namespace Arnested\Log;

use Arnested\Log\Journald;
use PHPUnit\Framework\TestCase;
use FFI\Exception as FFIException;

class SystemdSharedObjectTest extends TestCase
{

    public function testInvalidSharedObject(): void
    {
        try {
            new Journald(['systemd_shared_object' => 'invalid so file']);

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
            new Journald([
                'systemd_shared_object' => 'invalid so file',
                'ignore_missing_systemd' => true,
            ]);
            $this->assertTrue(true);
        } catch (\Throwable $t) {
            $this->assertNotInstanceOf(FFIException::class, $t);
        }
    }
}
