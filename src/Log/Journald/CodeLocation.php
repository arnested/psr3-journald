<?php

declare(strict_types=1);

namespace Arnested\Log\Journald;

final class CodeLocation
{
    /**
     * @var int|null $line
     */
    public ?int $line = null;

    /**
     * @var string|null $file
     */
    public ?string $file = null;

    /**
     * @var string|null $function
     */
    public ?string $function = null;

    /**
     * Convert to a systemd journald fields array.
     *
     * @return array<string|int>
     */
    public function toFields(): array
    {
        $fields = [];

        if (is_string($this->file)) {
            $fields[] = 'CODE_FILE=%s';
            $fields[] = $this->file;
        }

        if (is_string($this->function)) {
            $fields[] = 'CODE_FUNC=%s';
            $fields[] = $this->function;
        }

        if (is_int($this->line)) {
            $fields[] = 'CODE_LINE=%i';
            $fields[] = $this->line;
        }

        return $fields;
    }

    /**
     * @param array<string, int> $wrappers
     *
     * @return self
     */
    public static function new($wrappers = []): self
    {
        $wrappers += [
            __CLASS__ => 1,
        ];

        $limit = (int) array_sum($wrappers) + 1;

        $cl = new static();

        $stack = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $limit);

        for ($i = 0, $stackCount = count($stack); $i < $stackCount; ++$i) {
            $cl->file = $stack[$i]['file'] ?? null;
            $cl->line = $stack[$i]['line'] ?? null;

            if (in_array($stack[$i]['class'], array_keys($wrappers))) {
                continue;
            }

            $cl->file = $stack[$i - 1]['file'] ?? null;
            $cl->line = $stack[$i - 1]['line'] ?? null;
            $cl->function = implode('::', array_filter([
                $stack[$i]['class'] ?? null,
                $stack[$i]['function'] ?? null,
            ]));

            return $cl;
        }

        return $cl;
    }
}
