<?php

namespace Arnested\Log\Journald;

interface SenderInterface
{
    /**
     * @param array<string> $args
     */
    public function send(array $args);
}
