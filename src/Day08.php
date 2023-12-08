<?php

declare(strict_types=1);

namespace Dannyvdsluijs\AdventOfCode2023;

use Dannyvdsluijs\AdventOfCode2023\Concerns\ContentReader;

class Day08
{
    use ContentReader;

    public function partOne(): string
    {
        [$lines, $instructions, $instructionSize] = $this->parseInput();

        $current = 'AAA';
        $instructionPointer = 0;
        $steps = 0;
        while($current !== 'ZZZ') {
            $current = $lines[$current][$instructions[$instructionPointer]];
            $instructionPointer++;
            $steps++;
            $instructionPointer %= $instructionSize;
        }
        return (string) $steps;
    }

    public function partTwo(): string
    {
        [$lines, $instructions, $instructionSize] = $this->parseInput();

        $startingPoints = array_filter($lines, fn($v, $k) => str_ends_with($k, 'A'), ARRAY_FILTER_USE_BOTH);

        $currentPositions = array_combine(array_keys($startingPoints), array_keys($startingPoints),);

        $loopSize = [];
        foreach ($currentPositions as $start => $position) {
            $steps = 0;
            $instructionPointer = 0;
            while(!str_ends_with($position, 'Z')) {
                $position = $lines[$position][$instructions[$instructionPointer]];
                    $instructionPointer++;
                    $steps++;
                    $instructionPointer %= $instructionSize;
            }
            $loopSize[$start] = $steps;
        }

        $divisors = [];
        foreach ($loopSize as $start => $size) {
            $max = floor(sqrt($size));
            for ($x = 2; $x <= $max; $x++) {
                if ($size % $x === 0) {
                    $divisors[] = $y = intdiv($size, $x);
                    $divisors[] = intdiv($size, $y);
                }
            }
        }

        $uniqueDivisors = array_unique($divisors);

        return (string) array_product($uniqueDivisors);
    }

    public function parseInput(): array
    {
        $lines = $this->readInputAsLines();
        $instructions = str_split(array_shift($lines));
        $instructionSize = count($instructions);

        // Clear empty line
        array_shift($lines);

        $lines = array_map(
            static function (string $line) {
                $line = str_replace([' = (', ', ', ')'], ' ', $line);
                [$from, $left, $right] = explode(' ', $line);
                return [
                    'from' => $from,
                    'L' => $left,
                    'R' => $right
                ];
            },
            $lines,
        );

        $keys = array_column($lines, 'from');
        $lines = array_combine($keys, $lines);
        return array($lines, $instructions, $instructionSize);
    }
}