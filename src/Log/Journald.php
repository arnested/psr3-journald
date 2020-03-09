<?php

declare(strict_types=1);

namespace Arnested\Log;

use FFI\Exception as FFIException;
use Psr\Log\AbstractLogger;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LogLevel;

class Journald extends AbstractLogger
{
    /**
     * @var \FFI $ffi
     */
    protected ?\FFI $ffi = null;

    /**
     * @var array<string,int> $wrappers
     */
    protected array $wrappers = [
        __CLASS__ => 3,
    ];

    /**
     * @var string $systemdSharedObject
     */
    protected string $systemdSharedObject = 'libsystemd.so.0';

    /**
     * @var bool $ignoreMissingSystemd
     */
    protected bool $ignoreMissingSystemd = false;

    /**
     * Construct PSR-3 logger for journald.
     */
    public function __construct(array $options = [])
    {
        if (array_key_exists('systemd_shared_object', $options) && is_string($options['systemd_shared_object'])) {
            $this->systemdSharedObject = $options['systemd_shared_object'];
        }

        if (array_key_exists('ignore_missing_systemd', $options) && is_bool($options['ignore_missing_systemd'])) {
            $this->ignoreMissingSystemd = $options['ignore_missing_systemd'];
        }

        try {
            $this->ffi = \FFI::cdef(
                'int sd_journal_send(const char *format, ...);',
                $this->systemdSharedObject,
            );
        } catch (FFIException $e) {
            if (!$this->ignoreMissingSystemd) {
                throw $e;
            }
        }
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

        if ($this->ignoreMissingSystemd && !method_exists($this->ffi, 'sd_journal_send')) {
            return;
        }

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
    protected function logLevel($level): int
    {
        $logLevels = [
            LogLevel::EMERGENCY => 0,
            LogLevel::ALERT => 1,
            LogLevel::CRITICAL => 2,
            LogLevel::ERROR => 3,
            LogLevel::WARNING => 4,
            LogLevel::NOTICE => 5,
            LogLevel::INFO => 6,
            LogLevel::DEBUG => 7,
        ];

        if (is_object($level) || method_exists($level, '__toString')) {
            $level = (string) $level;
        }

        if (is_string($level) && array_key_exists($level, $logLevels)) {
            $level = $logLevels[$level];
        }

        if (is_numeric($level)) {
            $level = intval($level);
        }

        if (!is_int($level)) {
            throw new InvalidArgumentException('Unknown log level');
        }

        if (($level < min($logLevels) || ($level > max($logLevels)))) {
            throw new InvalidArgumentException('Log level out of range');
        }

        return $level;
    }
}
