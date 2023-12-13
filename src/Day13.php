<?php

declare(strict_types=1);

namespace Dannyvdsluijs\AdventOfCode2023;

use Dannyvdsluijs\AdventOfCode2023\Concerns\ContentReader;

class Day13
{
    use ContentReader;

    public function partOne(): string
    {
        $content = $this->readInput();
        $blocks = explode("\n\n", $content);
        $results = [];

        foreach ($blocks as $blockIndex => $block) {
            $grid = array_map(str_split(...), explode("\n", $block));
            $width = count($grid[0]);
            $depth = count($grid);
            $columns = array_map(fn(int $i) => array_column($grid, $i), range(0, $width -1));

            for ($x = 0; $x < $depth - 1; $x++) {
                if ($grid[$x] === $grid[$x +1] && $this->isPerfectReflection($grid, $x)) {
                    $results[$blockIndex] = ($x + 1) * 100;
                }
            }


            for ($y = 0; $y < $width - 1; $y++) {
                if ($columns[$y] === $columns[$y +1] && $this->isPerfectReflection($columns, $y)) {
                    $results[$blockIndex] = $y + 1;
                }
            }
        }

        return (string) array_sum($results);
    }

    public function partTwo(): string
    {
        $content = $this->readInput();
        $blocks = explode("\n\n", $content);
        $results = [];

        foreach ($blocks as $blockIndex => $block) {
            $grid = array_map(str_split(...), explode("\n", $block));
            $width = count($grid[0]);
            $depth = count($grid);
            $columns = array_map(static fn(int $i) => array_column($grid, $i), range(0, $width -1));

            for ($x = 0; $x < $depth - 1; $x++) {
                if ($this->isNearPerfectReflection($grid, $x)) {
                    $results[$blockIndex] = ($x + 1) * 100;
                }
            }


            for ($y = 0; $y < $width - 1; $y++) {
                if ($this->isNearPerfectReflection($columns, $y)) {
                    $results[$blockIndex] = $y + 1;
                }
            }
        }

        return (string) array_sum($results);
    }

    private function isPerfectReflection(array $rowsOrColumns, int $offset): bool
    {
        [$left, $right] = $this->getLeftAndRightOfOffset($rowsOrColumns, $offset);

        return $left === $right;
    }

    private function isNearPerfectReflection(array $rowsOrColumns, int $offset): bool
    {
        [$left, $right] = $this->getLeftAndRightOfOffset($rowsOrColumns, $offset);

        return levenshtein(
            implode(array_map(implode(...), $left)),
            implode(array_map(implode(...), $right)),
        ) === 1;
    }

    private function getLeftAndRightOfOffset(array $rowsOrColumns, int $offset): array
    {
        $depth = count($rowsOrColumns);

        $numberOfRowsOrColumnsAfterReflectionCenter = $depth - $offset - 1;
        if ($numberOfRowsOrColumnsAfterReflectionCenter > $offset) {
            $left = array_slice($rowsOrColumns, 0, $offset + 1);
            $right = array_reverse(array_slice($rowsOrColumns, $offset + 1, $offset + 1));

            return [$left, $right];
        }

        $left = array_slice($rowsOrColumns, $offset - $numberOfRowsOrColumnsAfterReflectionCenter + 1, $numberOfRowsOrColumnsAfterReflectionCenter);
        $right = array_reverse(array_slice($rowsOrColumns, $offset + 1));
        return [$left, $right];
    }
}