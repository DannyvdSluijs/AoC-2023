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
        $lines = $this->readInputAsLines();
        $numbersPerLine = [];
        foreach ($lines as $lineNumber => $line) {
            $numbersPerLine[$lineNumber] = [];

            for($x = 0, $len = strlen($line); $x < $len; $x++) {
                $char = substr($line, $x, 1);
                $three = substr($line, $x, 3);
                $four = substr($line, $x, 4);
                $five = substr($line, $x, 5);
                if (is_numeric($char)) {
                    $numbersPerLine[$lineNumber][] = (int) $char;
                    continue;
                }

                $threeReplacement = str_replace(
                    ['one', 'two', 'six'],
                    [1, 2, 6],
                    $three
                );
                if ($three !== $threeReplacement) {
                    $numbersPerLine[$lineNumber][] = (int) $threeReplacement;
                    continue;
                }

                $fourReplacement = str_replace(
                    ['four', 'five', 'nine'],
                    [4, 5, 9],
                    $four
                );
                if ($four !== $fourReplacement) {
                    $numbersPerLine[$lineNumber][] = (int) $fourReplacement;
                    continue;
                }

                $fiveReplacement = str_replace(
                    ['three', 'seven', 'eight'],
                    [3, 7, 8],
                    $five
                );
                if ($five !== $fiveReplacement) {
                    $numbersPerLine[$lineNumber][] = (int) $fiveReplacement;
                    continue;
                }
            }
        }

        $result = [];
        foreach ($numbersPerLine as $numbers) {
            $firstKey = array_key_first($numbers);
            $lastKey = array_key_last($numbers);

            $result[] = 10 * $numbers[$firstKey] + $numbers[$lastKey];
        }

        return (string) array_sum($result);
    }

}