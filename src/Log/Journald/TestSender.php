<?php

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
    public function send(array $args)
    {
        $this->fields = $args;
    }
}
