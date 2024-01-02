<?php

declare(strict_types=1);

namespace Dannyvdsluijs\AdventOfCode2023;

use Dannyvdsluijs\AdventOfCode2023\Concerns\ContentReader;

class Day14
{
    use ContentReader;

    public function partOne(): string
    {
        $rows = $this->readInputAsGridOfCharacters();
        $rows = $this->tiltNorth($rows);
        return (string) $this->computeLoadOfNorthSupportBeam($rows);
    }

    public function partTwo(): string
    {
        $rows = $this->readInputAsGridOfCharacters();
        $cycles = 1_000_000_000;
        $results = [];

        for ($cycle = 0; $cycle < $cycles; $cycle++) {
            $rows = $this->tiltNorth($rows);
            $rows = $this->tiltWest($rows);
            $rows = $this->tiltSouth($rows);
            $rows = $this->tiltEast($rows);

            if (in_array($rows, $results, true)) {
                $match = array_search($rows, $results, true);
                $loopSize = $cycle - $match;
                $remainingCycles = $cycles - $cycle;
                $reduced = $remainingCycles % $loopSize;
                $cycles = $cycle + $reduced;
            }
            $results[] = $rows;
        }

        return (string) $this->computeLoadOfNorthSupportBeam($rows);
    }

    private function computeLoadOfNorthSupportBeam(array $grid): int
    {
        $depth = count($grid);


        $score = 0;
        foreach ($grid as $index => $row) {
            $count = substr_count(implode($row), 'O');
            $score += ($depth - $index) * $count;
        }

        return $score;
    }

    private function tiltNorth(array $rows): array
    {
        $depth = count($rows);
        $width = count($rows[0]);

        // Rows to columns for tilt to north
        $columns = [];
        for ($x = 0; $x < $width; $x++) {
            $columns[] = array_column($rows, $x);
        }
        for ($x = 0; $x < $width; $x++) {
            $oCount = 0;
            $dotCount = 0;
            $result = [];
            for ($y = 0; $y < $depth; $y++) {
                if ($columns[$x][$y] === '.') {
                    $dotCount++;
                    continue;
                }
                if ($columns[$x][$y] === 'O') {
                    $oCount++;
                    continue;
                }

                $result[] = array_fill(0, $oCount, 'O');
                $result[] = array_fill(0, $dotCount, '.');
                $result[] = ['#'];
                $oCount = 0;
                $dotCount = 0;
            }
            $result[] = array_fill(0, $oCount, 'O');
            $result[] = array_fill(0, $dotCount, '.');

            $columns[$x] = array_merge(...$result);
        }

        // Columns to rows for tilt to west
        $rows = [];
        for ($x = 0; $x < $depth; $x++) {
            $rows[] = array_column($columns, $x);
        }

        return $rows;
    }

    private function tiltWest(array $rows): array
    {
        $width = count($rows[0]);

        foreach ($rows as $x => $xValue) {
            $oCount = 0;
            $dotCount = 0;
            $result = [];
            for ($y = 0; $y < $width; $y++) {
                if ($xValue[$y] === '.') {
                    $dotCount++;
                    continue;
                }
                if ($xValue[$y] === 'O') {
                    $oCount++;
                    continue;
                }

                $result[] = array_fill(0, $oCount, 'O');
                $result[] = array_fill(0, $dotCount, '.');
                $result[] = ['#'];
                $oCount = 0;
                $dotCount = 0;
            }
            $result[] = array_fill(0, $oCount, 'O');
            $result[] = array_fill(0, $dotCount, '.');

            $rows[$x] = array_merge(...$result);
        }

        return $rows;
    }

    private function tiltSouth(array $rows): array
    {
        $depth = count($rows);
        $width = count($rows[0]);
        // Rows to columns for tilt to south
        $columns = [];
        for ($x = 0; $x < $width; $x++) {
            $columns[] = array_column($rows, $x);
        }
        for ($x = 0; $x < $width; $x++) {
            $oCount = 0;
            $dotCount = 0;
            $result = [];
            for ($y = $depth - 1; $y >= 0; $y--) {
                if ($columns[$x][$y] === '.') {
                    $dotCount++;
                    continue;
                }
                if ($columns[$x][$y] === 'O') {
                    $oCount++;
                    continue;
                }

                $result[] = array_fill(0, $oCount, 'O');
                $result[] = array_fill(0, $dotCount, '.');
                $result[] = ['#'];
                $oCount = 0;
                $dotCount = 0;
            }
            $result[] = array_fill(0, $oCount, 'O');
            $result[] = array_fill(0, $dotCount, '.');

            $columns[$x] = array_reverse(array_merge(...$result));
        }

        // Columns to rows for tilt to east
        $rows = [];
        for ($x = 0; $x < $depth; $x++) {
            $rows[] = array_column($columns, $x);
        }

        return $rows;
    }

    private function tiltEast(array $rows): array
    {
        $width = count($rows[0]);
        foreach ($rows as $x => $xValue) {
            $oCount = 0;
            $dotCount = 0;
            $result = [];
            for ($y = $width - 1; $y >= 0; $y--) {
                if ($xValue[$y] === '.') {
                    $dotCount++;
                    continue;
                }
                if ($xValue[$y] === 'O') {
                    $oCount++;
                    continue;
                }

                $result[] = array_fill(0, $oCount, 'O');
                $result[] = array_fill(0, $dotCount, '.');
                $result[] = ['#'];
                $oCount = 0;
                $dotCount = 0;
            }
            $result[] = array_fill(0, $oCount, 'O');
            $result[] = array_fill(0, $dotCount, '.');

            $rows[$x] = array_reverse(array_merge(...$result));
        }

        return $rows;
    }
}