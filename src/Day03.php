<?php

declare(strict_types=1);

namespace Dannyvdsluijs\AdventOfCode2023;

use Dannyvdsluijs\AdventOfCode2023\Concerns\ContentReader;

class Day03
{
    use ContentReader;

    public function partOne(): string
    {
        $grid = $this->readInputAsGridOfCharacters();
        $numbersWithAdjacentSymbol = [];
        $rowLength = count($grid[0]);

        foreach ($grid as $lineNo => $line) {
            $buffer = '';
            foreach ($line as $column => $char) {
                if (!is_numeric($char)) {
                    if ($buffer === '') {
                        continue;
                    }
                    $number = (int) $buffer;

                    $length = strlen($buffer);

                    // See if symbol is adjacent
                    $sliceOffset = max($column - $length - 1, 0);
                    $sliceLength = min($rowLength - $sliceOffset, $length + 2);
                    if ($column - $length - 1 < 0) {
                        $sliceLength--;
                    }
                    $rowAbove = array_slice($grid[$lineNo - 1] ?? [], $sliceOffset, $sliceLength);
                    $rowBelow = array_slice($grid[$lineNo + 1] ?? [], $sliceOffset, $sliceLength);
                    $left = $grid[$lineNo][$column - $length - 1] ?? '';
                    $right = $char;

                    $allNonDots = array_filter([...$rowAbove, ...$rowBelow, $left, $right], static fn(string $char): bool => $char !== '.' && $char !== '');
                    if ($allNonDots !== []) {
                        $numbersWithAdjacentSymbol[] = $number;
                    }
                    $buffer = '';
                    continue;
                }

                $buffer .= $char;
            }

            if ($buffer === '') {
                continue;
            }
            $number = (int)$buffer;

            $length = strlen($buffer);

            // See if symbol is adjacent
            $sliceOffset = max($column - $length, 0);
            $sliceLength = min($rowLength - $sliceOffset, $length + 2);
            if ($sliceOffset === 0) {
                $sliceLength--;
            }
            $rowAbove = array_slice($grid[$lineNo - 1] ?? [], $sliceOffset, $sliceLength);
            $rowBelow = array_slice($grid[$lineNo + 1] ?? [], $sliceOffset, $sliceLength);
            $left = $grid[$lineNo][$column - $length] ?? '';
            $right = '';

            $allNonDots = array_filter([...$rowAbove, ...$rowBelow, $left, $right], static fn(string $char): bool => $char !== '.' && $char !== '');

            if ($allNonDots !== []) {
                $numbersWithAdjacentSymbol[] = $number;
            }
        }

        return (string) array_sum($numbersWithAdjacentSymbol);
    }

    public function partTwo(): string
    {
        $grid = $this->readInputAsGridOfCharacters();
        $lineLength = count($grid[0]);
        $gears = [];

        foreach ($grid as $lineNo => $line) {
            $buffer = '';
            foreach ($line as $column => $char) {
                if (!is_numeric($char)) {
                    if ($buffer === '') {
                        continue;
                    }
                    $number = (int) $buffer;

                    $length = strlen($buffer);

                    // See if symbol is adjacent
                    $sliceOffset = max($column - $length - 1, 0);
                    $sliceLength = min($lineLength - $sliceOffset, $length + 2);
                    if ($column - $length - 1 < 0) {
                        $sliceLength--;
                    }
                    $rowAbove = array_slice($grid[$lineNo - 1] ?? [], $sliceOffset, $sliceLength);
                    $rowBelow = array_slice($grid[$lineNo + 1] ?? [], $sliceOffset, $sliceLength);
                    $left = $grid[$lineNo][$column - $length - 1] ?? '';
                    $right = $char;

                    $stars = array_filter([...$rowAbove, ...$rowBelow, $left, $right], static fn(string $char): bool => $char === '*');
                    if ($stars !== []) {
                        if ($left === '*') {
                            $gearPosition = $lineNo * $lineLength + $column - $length;
                        }
                        if ($right === '*') {
                            $gearPosition = $lineNo * $lineLength + $column + 1;
                        }
                        if (($pos = array_search('*', $rowAbove, true)) !== false) {
                            $gearPosition = ($lineNo - 1) * $lineLength + $column - $length + $pos;
                            if ($column - $length - 1 < 0) {
                                $gearPosition++;
                            }
                        }
                        if (($pos = array_search('*', $rowBelow, true)) !== false) {
                            $gearPosition = ($lineNo + 1) * $lineLength + $column - $length + $pos;
                            if ($column - $length - 1 < 0) {
                                $gearPosition++;
                            }
                        }

                        $gears[$gearPosition] ??= [];
                        $gears[$gearPosition][] = $number;

                        $gearPosition = null;
                    }
                    $buffer = '';
                    continue;
                }

                $buffer .= $char;
            }

            if ($buffer === '') {
                continue;
            }
            $number = (int)$buffer;

            $length = strlen($buffer);

            // See if symbol is adjacent
            $sliceOffset = max($column - $length, 0);
            $sliceLength = min($lineLength - $sliceOffset, $length + 2);
            if ($sliceOffset === 0) {
                $sliceLength--;
            }
            $rowAbove = array_slice($grid[$lineNo - 1] ?? [], $sliceOffset, $sliceLength);
            $rowBelow = array_slice($grid[$lineNo + 1] ?? [], $sliceOffset, $sliceLength);
            $left = $line[$column - $length] ?? '';
            $right = '';

            $stars = array_filter([...$rowAbove, ...$rowBelow, $left, $right], static fn(string $char): bool => $char === '*');
            if ($stars !== []) {
                $gearPosition = 0;
                if ($left === '*') {
                    $gearPosition = $lineNo * $lineLength + $column - $length;
                }
                if (($pos = array_search('*', $rowAbove, true)) !== false) {
                    $gearPosition = ($lineNo - 1) * $lineLength + $column - $length + $pos;
                    if ($column - $length - 1 < 0) {
                        $gearPosition++;
                    }
                    if ($column + $length - 1 > $lineLength) {
                        $gearPosition++;
                    }
                }
                if (($pos = array_search('*', $rowBelow, true)) !== false) {
                    $gearPosition = ($lineNo + 1) * $lineLength + $column - $length + $pos;
                    if ($column - $length - 1 < 0) {
                        $gearPosition++;
                    }
                    if ($column + $length - 1 > $lineLength) {
                        $gearPosition++;
                    }
                }

                $gears[$gearPosition] ??= [];
                $gears[$gearPosition][] = $number;
            }
        }

        ksort($gears);
        $actualGears = array_filter($gears, static fn(array $g) => count ($g) === 2);

        return (string) array_sum(array_map(static fn(array $g) => $g[0] * $g[1], $actualGears));
    }
}