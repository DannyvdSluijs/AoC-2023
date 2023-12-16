<?php

declare(strict_types=1);

namespace Dannyvdsluijs\AdventOfCode2023;

use Dannyvdsluijs\AdventOfCode2023\Concerns\ContentReader;

class Day14
{
    use ContentReader;

    public function partOne(): string
    {
        $grid = $this->readInputAsGridOfCharacters();
        $depth = count($grid);
        $width = count($grid[0]);

        $columns = [];
        $results = [];
        $scores = [];
        for ($x = 0; $x < $width; $x++) {
            $colum = array_column($grid, $x);
            $chunks = explode('#', implode($colum));
            $result = '';

            $columnScore = 0;
            $pointer = $depth;
            foreach ($chunks as $chunk) {
                for($o = 0, $maxO = substr_count($chunk, 'O'); $o < $maxO; $o++) {
                    $columnScore += $pointer;
                    $pointer--;
                }
                for($dot = 0, $maxDot = substr_count($chunk, '.'); $dot < $maxDot; $dot++) {
                    $pointer--;
                }
                $pointer--;
            }
            $scores[] = $columnScore;
         }

        return (string) array_sum($scores);
    }

    public function partTwo(): string
    {
        $content = $this->readInput();

        return '';
    }
}