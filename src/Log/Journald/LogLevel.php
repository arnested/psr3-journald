<?php

declare(strict_types=1);

namespace Arnested\Log\Journald;

use Psr\Log\InvalidArgumentException;
use Psr\Log\LogLevel as PSR3LogLevel;

class LogLevel
{
    public const EMERGENCY = 0;
    public const ALERT = 1;
    public const CRITICAL = 2;
    public const ERROR = 3;
    public const WARNING = 4;
    public const NOTICE = 5;
    public const INFO = 6;
    public const DEBUG = 7;

    /**
     * Mapping of PSR-3 log levels to journald log levels.
     */
    public const PSR3_MAP = [
        PSR3LogLevel::EMERGENCY => self::EMERGENCY,
        PSR3LogLevel::ALERT => self::ALERT,
        PSR3LogLevel::CRITICAL => self::CRITICAL,
        PSR3LogLevel::ERROR => self::ERROR,
        PSR3LogLevel::WARNING => self::WARNING,
        PSR3LogLevel::NOTICE => self::NOTICE,
        PSR3LogLevel::INFO => self::INFO,
        PSR3LogLevel::DEBUG => self::DEBUG,
    ];

    /**
     * Helper to convert Psr\Log\LogLevel's to journald integer level.
     *
     * @param mixed $level The log level as an int, a string (with a
     *   numeric value), or an object with a __toString() method
     *   giving a numeric value.
     *
     * @throws \Psr\Log\InvalidArgumentException
     *   If the level could not be parsed or is out of bounds.
     *
     * @return int
     *   The log level as an integer.
     */
    public static function normalize($level): int
    {
        // If we have a Stringable object we can cast the object to a
        // string for further processing.
        if ($level instanceof \Stringable) {
            $level = (string) $level;
        }

        // If the level is a numeric convert it to its integer value.
        if (is_numeric($level)) {
            $level = intval($level);
        }

        // If the level is a string and the string is a known PSR-3
        // log level we convert it to its corresponding integer value.
        if (is_string($level) && array_key_exists($level, self::PSR3_MAP)) {
            $level = self::PSR3_MAP[$level];
        }

        // If at this point we haven't got an integer value. We bail
        // out because we are unable to convert it to an integer.
        if (!is_int($level)) {
            throw new InvalidArgumentException('Unknown log level');
        }

        // Now we know we have an integer value. Bail out if the
        // integer value is below or beyond journald's levels.
        if (($level < min(self::PSR3_MAP) || ($level > max(self::PSR3_MAP)))) {
            throw new InvalidArgumentException('Log level out of range');
        }

        return $level;
    }
}
