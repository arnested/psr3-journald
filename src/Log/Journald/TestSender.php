<?php

declare(strict_types=1);

namespace Arnested\Log\Journald;

use Arnested\Log\Journald\SenderInterface;

class TestSender implements SenderInterface
{
    /**
     * @var array<string> $fields
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
