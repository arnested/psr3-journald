<?php

declare(strict_types=1);

namespace Arnested\Log\Journald;

interface SenderInterface
{
    /**
     * @param array<mixed> $args
     */
    public function send(array $args): void;
}
