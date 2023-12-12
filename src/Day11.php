<?php

declare(strict_types=1);

namespace Dannyvdsluijs\AdventOfCode2023;

use Dannyvdsluijs\AdventOfCode2023\Concerns\ContentReader;

class Day11
{
    use ContentReader;

    public function partOne(): string
    {
        $grid = $this->readInputAsGridOfCharacters();
        $depth = count($grid);
        $width = count($grid[0]);

        $galaxies = [0 => null];
        foreach ($grid as $x => $row) {
            foreach ($row as $y => $cell) {
                if ($cell === '#') {
                    $galaxies[] = [$x, $y];
                }
            }
        }
        $rowsWithGalaxies = array_column($galaxies, '0');
        $rowsWithoutGalaxies = array_filter(range(0, $depth - 1), fn ($v) => !in_array($v, $rowsWithGalaxies, true));
        $columnsWithGalaxies = array_column($galaxies, 1);
        $columnsWithoutGalaxies = array_filter(range(0, $width - 1), fn ($v) => !in_array($v, $columnsWithGalaxies, true));

        $distances = [];
        foreach ($galaxies as $key1 => $galaxy1) {
            if (is_null($galaxy1)) {
                continue;
            }
            foreach ($galaxies as $key2 => $galaxy2) {
                if (is_null($galaxy2)) {
                    continue;
                }
                $combined = min($key1, $key2) . '-' . max($key1, $key2);
                if ($key1 === $key2 || array_key_exists($combined, $distances)) {
                    continue;
                }

                $minX = min($galaxy1[0], $galaxy2[0]);
                $maxX = max($galaxy1[0], $galaxy2[0]);
                $minY = min($galaxy1[1], $galaxy2[1]);
                $maxY = max($galaxy1[1], $galaxy2[1]);
                $manhattan = ($maxX - $minX) + ($maxY - $minY);

                $rowsWithoutGalaxiesCrossed = count(array_filter($rowsWithoutGalaxies, static fn(int $row) => $row > $minX && $row < $maxX));
                $columnsWithoutGalaxiesCrossed = count(array_filter($columnsWithoutGalaxies, static fn(int $column) => $column > $minY && $column < $maxY));

                $distances[$combined] = [
                    'manhattan' => $manhattan,
                    'rowsWithoutGalaxiesCrossed' => $rowsWithoutGalaxiesCrossed,
                    'columnsWithoutGalaxiesCrossed' => $columnsWithoutGalaxiesCrossed,
                    'sum' => $manhattan + $rowsWithoutGalaxiesCrossed + $columnsWithoutGalaxiesCrossed
                ];
            }
        }


        return (string) array_sum(array_column($distances, 'sum'));
    }

    public function partTwo(): string
    {
        $grid = $this->readInputAsGridOfCharacters();
        $depth = count($grid);
        $width = count($grid[0]);

        $galaxies = [0 => null];
        foreach ($grid as $x => $row) {
            foreach ($row as $y => $cell) {
                if ($cell === '#') {
                    $galaxies[] = [$x, $y];
                }
            }
        }
        $rowsWithGalaxies = array_column($galaxies, '0');
        $rowsWithoutGalaxies = array_filter(range(0, $depth - 1), fn ($v) => !in_array($v, $rowsWithGalaxies, true));
        $columnsWithGalaxies = array_column($galaxies, 1);
        $columnsWithoutGalaxies = array_filter(range(0, $width - 1), fn ($v) => !in_array($v, $columnsWithGalaxies, true));

        $distances = [];
        foreach ($galaxies as $key1 => $galaxy1) {
            if (is_null($galaxy1)) {
                continue;
            }
            foreach ($galaxies as $key2 => $galaxy2) {
                if (is_null($galaxy2)) {
                    continue;
                }
                $combined = min($key1, $key2) . '-' . max($key1, $key2);
                if ($key1 === $key2 || array_key_exists($combined, $distances)) {
                    continue;
                }

                $minX = min($galaxy1[0], $galaxy2[0]);
                $maxX = max($galaxy1[0], $galaxy2[0]);
                $minY = min($galaxy1[1], $galaxy2[1]);
                $maxY = max($galaxy1[1], $galaxy2[1]);
                $manhattan = ($maxX - $minX) + ($maxY - $minY);

                $rowsWithoutGalaxiesCrossed = count(array_filter($rowsWithoutGalaxies, static fn(int $row) => $row > $minX && $row < $maxX));
                $columnsWithoutGalaxiesCrossed = count(array_filter($columnsWithoutGalaxies, static fn(int $column) => $column > $minY && $column < $maxY));

                $distances[$combined] = [
                    'manhattan' => $manhattan,
                    'rowsWithoutGalaxiesCrossed' => $rowsWithoutGalaxiesCrossed,
                    'columnsWithoutGalaxiesCrossed' => $columnsWithoutGalaxiesCrossed,
                    'sum' => $manhattan + ($rowsWithoutGalaxiesCrossed * 999_999) + ($columnsWithoutGalaxiesCrossed * 999_999)
                ];
            }
        }


        return (string) array_sum(array_column($distances, 'sum'));
    }
}