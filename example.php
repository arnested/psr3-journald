<?php

require 'vendor/autoload.php';

require 'example/class.inc';
require 'example/function.inc';

use Arnested\Log\Journald;

$logger = new Journald([
    'add_code_location' => true,
]);

$logger->debug('Hello world from Psr\Log\LoggerInterface::debug()');

new \Example\LogFromClass($logger);

echo logFromFunction($logger);
