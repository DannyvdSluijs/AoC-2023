<?php

declare(strict_types=1);

namespace Dannyvdsluijs\AdventOfCode2023;

use Dannyvdsluijs\AdventOfCode2023\Concerns\ContentReader;

class Day01
{
    use ContentReader;

    public function partOne(): string
    {
        return (string) array_sum(
            array_map(
                static fn(array $n): int => 10 * $n[array_key_first($n)] + $n[array_key_last($n)],
                array_map(
                    static fn(array $line): array => array_filter($line, is_numeric(...)),
                    $this->readInputAsGridOfCharacters()
                )
            )
        );
    }

    public function partTwo(): string
    {
        return (string) array_sum(
            array_map(
                static fn(array $n): int => 10 * $n[array_key_first($n)] + $n[array_key_last($n)],
                array_map(
                    static fn(string $line): array => array_filter(
                        str_split(
                            str_replace(
                                ['one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'],
                                ['one1one', 'two2two', 'three3three', 'four4four', 'five5five', 'six6six', 'seven7seven', 'eight8eight', 'nine9nine'],
                                $line
                            )
                        ),
                        is_numeric(...),
                    ),
                    $this->readInputAsLines(),
                )
            )
        );
    }
}