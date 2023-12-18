<?php

declare(strict_types=1);

namespace Dannyvdsluijs\AdventOfCode2023;

use Dannyvdsluijs\AdventOfCode2023\Concerns\ContentReader;

class Day18
{
    use ContentReader;

    public function partOne(): string
    {
        $instructions = array_map(function(string $line): array {
            [$direction, $amount, $color] = explode(' ', $line);
            return [
                'direction' => $direction,
                'amount' => (int) $amount,
                'color' => substr($color, 2, -1),
            ];
        }, $this->readInputAsLines());

        // Collect dimensions & start offset
        $pos = [0, 0];
        $x = [];
        $y = [];
        foreach($instructions as $instruction) {
            switch ($instruction['direction']) {
                case 'D':
                    $pos[0] += $instruction['amount'];
                    break;
                case 'U':
                    $pos[0] -= $instruction['amount'];
                    break;
                case 'R':
                    $pos[1] += $instruction['amount'];
                    break;
                case 'L':
                    $pos[1] -= $instruction['amount'];
                    break;
            }
            $x[] = $pos[0];
            $y[] = $pos[1];
        }

        $depth = max($x) - min($x) + 1;
        $width = max($y) - min($y) + 1;
        $xOffset = abs(min($x));
        $yOffset = abs(min($y));

        // Dig trench
        $grid = array_fill(0, $depth, array_fill(0, $width, '.'));
        $pos = [$xOffset, $yOffset];
        $grid[$pos[0]][$pos[1]] = '#';
        foreach($instructions as $instruction) {
            for($x = 0; $x < $instruction['amount']; $x++) {
                switch ($instruction['direction']) {
                    case 'D':
                        $pos[0] += 1;
                        break;
                    case 'U':
                        $pos[0] -= 1;
                        break;
                    case 'R':
                        $pos[1] += 1;
                        break;
                    case 'L':
                        $pos[1] -= 1;
                        break;
                }
                $grid[$pos[0]][$pos[1]] = '#';
            }
        }

        // Second row after first hash start flood fill
        $pos = [1, array_search('#', $grid[1], true) + 1];
        $grid = $this->replaceNeighbouringGroundsWith($grid, $pos, '#');
        $result = array_reduce($grid, fn(int $carry, array $line) => $carry += array_count_values($line)['#'] ?? 0, 0);

        return (string) $result;
    }

    public function partTwo(): string
    {
        return '';
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
}