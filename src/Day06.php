<?php

declare(strict_types=1);

namespace Dannyvdsluijs\AdventOfCode2023;

use Dannyvdsluijs\AdventOfCode2023\Concerns\ContentReader;

class Day06
{
    use ContentReader;

    public function partOne(): string
    {
        $lines = $this->readInputAsLines();
        [$times, $distances] = array_map(function(string $line) {
            $line = trim(substr($line, 9));
            $numbers = explode(' ', $line);
            $numbers = array_filter($numbers);
            return array_map(intval(...), $numbers);
        }, $lines);
        $times = array_values($times);
        $distances = array_values($distances);

        $waysToWin = [];
        for($race = 0, $races = count($times); $race < $races; $race++) {
            $time = $times[$race];
            $distance = $distances[$race];

            for ($hold = 1; $hold < $time; $hold++) {
                $thisDistance = $hold * ($time - $hold);
                if ($thisDistance > $distance) {
                    $lowestHold = $hold;
                    break;
                }
            }
            for ($hold = $time - 1; $hold >0; $hold--) {
                $thisDistance = $hold * ($time - $hold);
                if ($thisDistance > $distance) {
                    $highestHold = $hold;
                    break;
                }
            }

            $waysToWin[$race] = $highestHold - $lowestHold + 1;
        }

        return (string) array_product($waysToWin);
    }

    public function partTwo(): string
    {
        $lines = $this->readInputAsLines();
        [$time, $distance] = array_map(function (string $line) {
            $line = trim(substr($line, 9));
            $numbers = explode(' ', $line);
            $numbers = array_filter($numbers);
            return (int)implode($numbers);
        }, $lines);


        for ($hold = 1; $hold < $time; $hold++) {
            $thisDistance = $hold * ($time - $hold);
            if ($thisDistance > $distance) {
                $lowestHold = $hold;
                break;
            }
        }
        for ($hold = $time - 1; $hold > 0; $hold--) {
            $thisDistance = $hold * ($time - $hold);
            if ($thisDistance > $distance) {
                $highestHold = $hold;
                break;
            }
        }

        return (string) ($highestHold - $lowestHold + 1);
    }
}