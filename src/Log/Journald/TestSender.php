<?php

declare(strict_types=1);

namespace Arnested\Log\Journald;

use Arnested\Log\Journald\SenderInterface;

class TestSender implements SenderInterface
{
    /**
     * @var array<mixed> $fields
     */
    protected array $fields;

    /**
     * {@inheritDoc}
     */
    public function send(array $args): void
    {
        $this->fields = $args;
    }
}
