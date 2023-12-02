<?php

declare(strict_types=1);

namespace Dannyvdsluijs\AdventOfCode2023;

use Dannyvdsluijs\AdventOfCode2023\Concerns\ContentReader;

class Day02
{
    use ContentReader;

    public function partOne(): string
    {
        $games = $this->parseData();
        $result = 0;

        foreach ($games as $game) {
            foreach ($game['sets'] as $set) {
                foreach ($set as $cube) {
                    if ($cube['color'] === 'red' && $cube['amount'] > 12) {
                        continue 3;
                    }
                    if ($cube['color'] === 'green' && $cube['amount'] > 13) {
                        continue 3;
                    }
                    if ($cube['color'] === 'blue' && $cube['amount'] > 14) {
                        continue 3;
                    }
                }
            }

            $result += $game['number'];
        }

        return (string) $result;
    }

    public function partTwo(): string
    {
        $games = $this->parseData();
        $result = 0;

        foreach ($games as $game) {
            $maxRed = 0;
            $maxGreen = 0;
            $maxBlue = 0;
            foreach ($game['sets'] as $set) {
                foreach ($set as $cube) {
                    if ($cube['color'] === 'red') {
                        $maxRed = max($maxRed, $cube['amount']);
                    }
                    if ($cube['color'] === 'green') {
                        $maxGreen = max($maxGreen, $cube['amount']);
                    }
                    if ($cube['color'] === 'blue') {
                        $maxBlue = max($maxBlue, $cube['amount']);
                    }
                }
            }

            $result += $maxRed * $maxGreen * $maxBlue;
        }

        return (string) $result;
    }

    public function parseData(): array
    {
        $content = $this->readInputAsLines();
        $games = [];
        foreach ($content as $line) {
            [$gameNumber, $setsAsString] = explode(':', $line);
            [, $gameNumber] = explode(' ', $gameNumber);
            $sets = [];
            foreach (explode(';', $setsAsString) as $cubeAsSet) {
                $cubes = [];
                foreach (explode(', ', $cubeAsSet) as $cube) {
                    $cubes[] = [
                        'amount' => (int)$cube,
                        'color' => match (substr($cube, -3)) {
                            'red' => 'red',
                            'lue' => 'blue',
                            'een' => 'green'
                        }
                    ];
                }
                $sets[] = $cubes;
            }

            $games[(int)$gameNumber] = [
                'number' => (int)$gameNumber,
                'sets' => $sets,
            ];
        }

        return $games;
    }
}