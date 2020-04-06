<?php

declare(strict_types=1);

namespace Arnested\Log;

use Arnested\Log\Journald\CodeLocation;
use PHPUnit\Framework\TestCase;

class CodeLocationTest extends TestCase
{

    public function testToFields(): void
    {
        $cl = new CodeLocation();
        $cl->line = 1337;
        $cl->file = 'foo.php';
        $cl->function = 'bar';

        $expected = [
            'CODE_FILE=%s',
            'foo.php',
            'CODE_FUNC=%s',
            'bar',
            'CODE_LINE=%i',
            1337,
        ];

        $this->assertEquals($expected, $cl->toFields());
    }

    public function testBacktrace(): void
    {
        [$expected, $cl] = $this->helper();

        $this->assertEquals($expected, $cl->toFields());
    }

    /**
     * @return array<mixed>
     */
    private function helper(): array
    {
        // phpcs:ignore Generic.Formatting.DisallowMultipleStatements.SameLine
        $cl = CodeLocation::new(); $line = __LINE__;

        $expected = [
            'CODE_FILE=%s',
            __FILE__,
            'CODE_FUNC=%s',
            __METHOD__,
            'CODE_LINE=%i',
            $line,
        ];

        return [$expected, $cl];
    }
}
