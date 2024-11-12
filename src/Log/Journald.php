<?php

declare(strict_types=1);

namespace Arnested\Log;

use Arnested\Log\Journald\LogLevel;
use Arnested\Log\Journald\Sender;
use Arnested\Log\Journald\SenderInterface;
use Arnested\Log\Journald\SystemdException;
use FFI\Exception as FFIException;
use Psr\Log\AbstractLogger;

class Journald extends AbstractLogger
{
    /**
     * @var \Arnested\Log\Journald\SenderInterface $sender
     */
    protected SenderInterface $sender;

    /**
     * Construct PSR-3 logger for journald.
     *
     * @throws \Arnested\Log\Journald\SystemdException
     *   If we could not instantiate a FFI systemd object.
     */
    public function __construct(?SenderInterface $sender = null)
    {
        if (!$sender instanceof SenderInterface) {
            try {
                $sender = new Sender();
            } catch (FFIException $e) {
                throw new SystemdException($e->getMessage(), $e->getCode(), $e);
            }
        }

        $this->sender = $sender;
    }

    /**
     * @param int|string|object $level
     * @param string|object|null $message
     * @param array<mixed> $context
     *
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        $exception = null;
        $fields = [];

        if ($message instanceof \Throwable) {
            $exception = $message;
            $message = $exception->getMessage();
        }

        if (isset($context['exception']) && $context['exception'] instanceof \Throwable) {
            $exception = $context['exception'];
            $fields[] = 'CODE_FILE=' . $exception->getFile();
            $fields[] = 'CODE_LINE=' . $exception->getLine();

            if ($message === null) {
                $message = $context['exception']->getMessage();
            }
        }

        if (is_object($message) && method_exists($message, '__toString')) {
            $message = (string) $message;
        }

        if (!is_string($message)) {
            $message = '';
        }

        if (isset($context['journald']) && is_array($context['journald'])) {
            $fields = $context['journald'];
        }

        foreach ($this->getPlaceholdes($message, $context) as $placeholder) {
            $fields[] = 'MESSAGE_PLACEHOLDERS=%s';
            $fields[] = $placeholder;
        }

        $level = LogLevel::normalize($level);

        $this->journaldSend($level, $message, $fields);
    }

    /**
     * Internal helper for structuring and sending to journald.
     *
     * @param int $level
     *   The logging level.
     * @param string $message
     *   The log message
     * @param array<mixed> $fields
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

        $this->sender->send($args);
    }

    /**
     * Extract placeholders and their values.
     *
     * @param string $message
     *   The message string with placeholders.
     * @param array<mixed> $context
     *   The context array with the placeholder values.
     *
     * @return array<string>
     *   An array with the placeholder/values pairs.
     */
    protected function getPlaceholdes(string $message, array $context): array
    {
        preg_match_all("/{([A-Za-z0-9_\.]+)}/", $message, $matches);

        $placeholders = [];

        foreach ($matches[1] as $placeholder) {
            if (!array_key_exists($placeholder, $context) || !is_string($context[$placeholder])) {
                // The placeholder is not present in the context array. Ignore.
                continue;
            }

            $placeholders[] = "{$placeholder}={$context[$placeholder]}";
        }

        return $placeholders;
    }
}
