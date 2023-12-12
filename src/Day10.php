<?php

declare(strict_types=1);

namespace Dannyvdsluijs\AdventOfCode2023;

use Dannyvdsluijs\AdventOfCode2023\Concerns\ContentReader;

class Day10
{
    use ContentReader;

    public function partOne(): string
    {
        $grid = $this->readInputAsGridOfCharacters();

        $start = $current = $this->findStart($grid);
        $startSymbol = $this->determineStartSymbol($grid, $current);
        $grid[$current[0]][$current[1]] = $startSymbol;

        $path = [$start];
        $previous = $start;
        do {
            $oneBeforePrevious = $previous;
            $previous = $current;
            $currentPipe = $grid[$current[0]][$current[1]];
            $options = match($currentPipe) {
                '|' => [$this->north($current), $this->south($current)],
                '-' => [$this->east($current), $this->west($current)],
                'L' => [$this->north($current), $this->east($current)],
                'J' => [$this->north($current), $this->west($current)],
                '7' => [$this->west($current), $this->south($current)],
                'F' => [$this->east($current), $this->south($current)],
            };

            $valid = array_filter($options, fn(array $option) => $option !== $oneBeforePrevious);
            $current = array_shift($valid);
            $path[] =  $current;
        } while ($current !== $start);

        return (string) ((count($path) - 1) / 2);
    }

    public function partTwo(): string
    {
        $grid = $this->readInputAsGridOfCharacters();

        $start = $current = $this->findStart($grid);
        $startSymbol = $this->determineStartSymbol($grid, $current);
        $grid[$current[0]][$current[1]] = $startSymbol;

        $path = [$start];
        $previous = $start;
        do {
            $oneBeforePrevious = $previous;
            $previous = $current;
            $currentPipe = $grid[$current[0]][$current[1]];
            $options = match($currentPipe) {
                '|' => [$this->north($current), $this->south($current)],
                '-' => [$this->east($current), $this->west($current)],
                'L' => [$this->north($current), $this->east($current)],
                'J' => [$this->north($current), $this->west($current)],
                '7' => [$this->west($current), $this->south($current)],
                'F' => [$this->east($current), $this->south($current)],
            };

            $valid = array_filter($options, fn(array $option) => $option !== $oneBeforePrevious);
            $current = array_shift($valid);
            $path[] =  $current;
        } while ($current !== $start);

        $xMax = count($grid) - 1;
        $yMax = count($grid[0]) -1;
        // Any leftover pipe that isn't in $path can be turned into ground `.`
        for ($x = 0; $x <= $xMax; $x++) {
            for ($y = 0; $y <= $yMax; $y++) {
                if ($grid[$x][$y] !== '.' && !in_array([$x, $y], $path)) {
                    $grid[$x][$y] = '.';
                }
            }
        }

        for ($x = 0; $x <= $xMax; $x++) {
            for ($y = 0; $y <= $yMax; $y++) {
                if (!($x === 0 || $y === 0 || $x === $xMax || $y === $yMax)) {
                    continue;
                }
                if ($grid[$x][$y] !== '.') {
                    continue;
                }
                $grid = $this->replaceNeighbouringGroundsWith($grid, [$x, $y], 'O');
            }
        }

        for ($x = 1; $x <= $xMax - 1; $x++) {
            for ($y = 1; $y <= $yMax -1; $y++) {
                if ($grid[$x][$y] !== '.') {
                    continue;
                }

                $everyThingEast = array_slice($grid[$x], $y + 1);
                $everythingWest = array_slice($grid[$x], 0, $y);
                $column = array_column($grid, $y);
                $everythingNorth = array_slice($column, 0, $x);
                $everythingSouth = array_slice($column, $x +1);

                // When checking horizontal the pipe `-` has no effect, same goes for `|` when checking vertical. Also
                // loop backs (`LJ`, `F7`, `FL`, `7J`) have no effect depending on the horizontal or vertical plane
                $filteredEverythingEast = implode(array_filter($everyThingEast, static fn (string $g) => in_array($g, ['|', 'F', 'J', 'L', '7'], true)));
                $eastCount = substr_count($filteredEverythingEast, '|') + substr_count($filteredEverythingEast, 'FJ') + substr_count($filteredEverythingEast, 'L7');
                if ($eastCount % 2 === 1) {
                    $grid = $this->replaceNeighbouringGroundsWith($grid, [$x, $y], 'I');
                    continue;
                }

                $filteredEverythingWest = implode(array_filter($everythingWest, static fn (string $g) => in_array($g, ['|', 'F', 'J', 'L', '7'], true)));
                $westCount = substr_count($filteredEverythingWest, '|') + substr_count($filteredEverythingWest, 'FJ') + substr_count($filteredEverythingWest, 'L7');
                if ($westCount % 2 === 1) {
                    $grid = $this->replaceNeighbouringGroundsWith($grid, [$x, $y], 'I');
                    continue;
                }

                $filteredEverythingNorth = implode(array_filter($everythingNorth, static fn (string $g) => in_array($g, ['-', 'F', 'J', 'L', '7'], true)));
                $northCount = substr_count($filteredEverythingNorth, '-') + substr_count($filteredEverythingNorth, 'FJ') + substr_count($filteredEverythingNorth, '7L');
                if ($northCount % 2 === 1) {
                    $grid = $this->replaceNeighbouringGroundsWith($grid, [$x, $y], 'I');
                    continue;
                }

                $filteredEverythingSouth = implode(array_filter($everythingSouth, static fn (string $g) => in_array($g, ['-', 'F', 'J', 'L', '7'], true)));
                $southCount = substr_count($filteredEverythingSouth, '-') + substr_count($filteredEverythingSouth, 'FJ') + substr_count($filteredEverythingSouth, '7L');
                if ($southCount % 2 === 1) {
                    $grid = $this->replaceNeighbouringGroundsWith($grid, [$x, $y], 'I');
                    continue;
                }

                $grid = $this->replaceNeighbouringGroundsWith($grid, [$x, $y], 'O');
            }
        }

        $insideCount = array_map(fn(array $row) => array_count_values($row)['I'] ?? 0, $grid);

        return (string) array_sum($insideCount);
    }

    public function findStart(array $grid): array
    {
        $start = null;
        for ($x = 0, $xMax = count($grid); $x < $xMax; $x++) {
            for ($y = 0, $yMax = count($grid[0]); $y < $yMax; $y++) {
                if ($grid[$x][$y] === 'S') {
                    $start = [$x, $y];
                }
            }
        }

        if (\is_null($start)) {
            throw new \RuntimeException('Unable to find start');
        }
        return $start;
    }

    public function determineStartSymbol(array $grid, array $current): string
    {
        $north = $this->north($current);
        $east = $this->east($current);
        $south = $this->south($current);
        $west = $this->west($current);

        $possible = [];
        if (in_array($grid[$north[0]][$north[1]] ?? '', ['|', '7', 'F'], true)) {
            $possible[] = 'north';
        }
        if (in_array($grid[$east[0]][$east[1]] ?? '', ['-', '7', 'J'], true)) {
            $possible[] = 'east';
        }
        if (in_array($grid[$south[0]][$south[1]] ?? '', ['|', 'L', 'J'], true)) {
            $possible[] = 'south';
        }
        if (in_array($grid[$west[0]][$west[1]] ?? '', ['-', 'L', 'F'], true)) {
            $possible[] = 'west';
        }

        sort($possible);

        return match ($possible) {
            ['north', 'south'] => '|',
            ['east', 'west'] => '-',
            ['east', 'north'] => 'L',
            ['north', 'west'] => 'J',
            ['south', 'west'] => '7',
            ['east', 'south'] => 'F',
        };
    }

    private function north(array $pos): array
    {
        return [$pos[0] - 1, $pos[1]];
    }

    private function east(array $pos): array
    {
        return [$pos[0], $pos[1] + 1];
    }

    private function south(array $pos): array
    {
        return [$pos[0] + 1, $pos[1]];
    }

    private function west(array $pos): array
    {
        return [$pos[0], $pos[1] - 1];
    }

    private function replaceNeighbouringGroundsWith(array $grid, array $current, string $replace): array
    {
        $north = $this->north($current);
        $east = $this->east($current);
        $south = $this->south($current);
        $west = $this->west($current);

        $grid[$current[0]][$current[1]] = $replace;

        foreach ([$north, $east, $south, $west] as $pos) {
            if (($grid[$pos[0]][$pos[1]] ?? '') === '.') {
                $grid[$pos[0]][$pos[1]] = $replace;
                $grid = $this->replaceNeighbouringGroundsWith($grid, $pos, $replace);
            }
        }

        return $grid;
    }

    private function pipeCount(array $sliceOfColumnOrRow): int
    {
        return count(array_filter($sliceOfColumnOrRow, fn ($g) => !in_array($g, ['.', 'I', 'O'])));
    }
}