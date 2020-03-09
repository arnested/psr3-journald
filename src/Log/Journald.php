<?php

namespace Arnested\Log;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class Journald extends AbstractLogger
{
    /**
     * @var \FFI $ffi
     */
    protected $ffi;

    /**
     * Construct PSR-3 logger for journald.
     */
    public function __construct()
    {
        $this->ffi = \FFI::cdef(
            "int sd_journal_send(const char *format, ...);",
            "libsystemd.so.0",
        );
    }

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = array())
    {
        $fields = [];

        if (isset($context['journald']) && is_array($context['journald'])) {
            $fields = $context['journald'];
        }

        if (isset($context['exception']) && $context['exception'] instanceof \Throwable) {
            $fields[] = 'CODE_FILE=' . $context['exception']->getFile();
            $fields[] = 'CODE_LINE=' . $context['exception']->getLine();

            if ($message === null) {
                $message = $context['exception']->getMessage();
            }
        }

        $this->journaldSend($this->logLevel($level), $message, $fields);
    }

    /**
     * Internal helper for structuring and sending to journald.
     *
     * @param int $level
     *   The logging level.
     * @param string $message
     *   The log message
     * @param array<string> $fields
     *   The fields to log to journald.
     */
    protected function journaldSend(int $level, string $message, array $fields): void
    {
        $args = [
            "MESSAGE=%s",
            $message,
            "PRIORITY=%i",
            $level,
            ...$fields,
            null,
        ];

        $this->ffi->sd_journal_send(...$args);
    }

    /**
     * Helper to convert Psr\Log\LogLevel's to integer.
     *
     * @param string|int $loglevel
     *   The log level as a string or an integer-
     *
     * @return int
     *   The log level as an integer.
     */
    protected function logLevel($loglevel): int
    {
        if (is_int($loglevel)) {
            return $loglevel;
        }

        switch ($loglevel) {
            case LogLevel::EMERGENCY:
                return 0;

            case LogLevel::ALERT:
                return 1;

            case LogLevel::CRITICAL:
                return 2;

            case LogLevel::ERROR:
                return 3;

            case LogLevel::WARNING:
                return 4;

            case LogLevel::NOTICE:
                return 5;

            case LogLevel::INFO:
                return 6;

            case LogLevel::DEBUG:
                return 7;

            default:
                throw new \LogicException("Unknown log level.");
        }
    }
}
