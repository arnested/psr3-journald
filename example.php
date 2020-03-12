<?php

require 'vendor/autoload.php';

use Arnested\Log\Journald;
use Psr\Log\LogLevel;

$logger = new Journald();

$logger->debug('Hello world from Psr\Log\LoggerInterface::debug()');

$logger->log(LogLevel::DEBUG, 'Hello world from Psr\Log\LoggerInterface::log()');

$logger->debug('Hello world with structured fields', [
    'journald' => [
        // Free form structured data -- some fields have special
        // meaning, see
        // http://0pointer.de/public/systemd-man/systemd.journal-fields.html
        'MY_FIELD=Kulli waffli',
        // You can repeat a field.
        'MY_REPEATED_FIELD=Zarka gunku',
        'MY_REPEATED_FIELD=Emfle birnan',
        // UTF-8 is supported.
        'MY_OTHER_FIELD=Smöja dunku',
        // You could actually override MESSAGE and PRIORITY here if
        // you wanted to obscure things...
    ],
]);

try {
    throw new \RuntimeException('Hello world from an exception (or a trowable)');
} catch (\Throwable $t) {
    // Logging an exception will set CODE_FILE and CODE_LINE from the
    // Exception. If the log message is NULL the message will be
    // extracted from the exception.
    $logger->error(null, ['exception' => $t]);
}

$logger->debug('Hello {location}. We are logging with {foo}.', [
    'location' => 'world',
    'foo' => 'placeholders',
]);

printf("To view the log entries run:\n\n$ journalctl -o json-pretty _PID=%s\n", getmypid());
