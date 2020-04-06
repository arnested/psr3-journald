<?php

declare(strict_types=1);

namespace Arnested\Log\Journald;

use Arnested\Log\Journald\SenderInterface;

class Sender implements SenderInterface
{
    /**
     * @var \FFI $ffi
     */
    protected \FFI $ffi;

    public function __construct(string $sharedObject = 'libsystemd.so.0')
    {
        $this->ffi = \FFI::cdef(
            'int sd_journal_send(const char *format, ...);',
            $sharedObject,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function send(array $args): void
    {
        $this->ffi->sd_journal_send(...$args);
    }
}
