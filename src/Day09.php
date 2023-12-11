<?php

declare(strict_types=1);

namespace Dannyvdsluijs\AdventOfCode2023;

use Dannyvdsluijs\AdventOfCode2023\Concerns\ContentReader;

class Day09
{
    use ContentReader;

    public function partOne(): string
    {
        $content = $this->readInputAsLines();
        $linesOfNumbers = array_map(fn(string $line): array => explode(' ', $line), $content);
        foreach ($linesOfNumbers as $key => $lineOfNumbers) {
            $linesOfNumbers[$key] = array_map(fn(string $number): int => (int) $number, $lineOfNumbers);
        }

        $results = [];
        foreach ($linesOfNumbers as $lineOfNumbers) {
            $layers = [$lineOfNumbers];
            $last = $lineOfNumbers;
            do {
                $forelast = $last;
                $last = [];
                for ($x = 0, $max = count($forelast) - 1; $x < $max; $x++) {
                    $last[] = $forelast[$x + 1] - $forelast[$x];
                }
                $layers[] = $last;
            } while (count(array_unique($last)) !== 1);
            $lastNumbers = array_map(static fn(array $numbers): int => array_pop($numbers), $layers);
            $results[] = ['layers' => $layers, 'next' => array_sum($lastNumbers)];
        }


        return (string) array_sum(array_column($results, 'next'));
    }

    public function partTwo(): string
    {
        $content = $this->readInputAsLines();
        $linesOfNumbers = array_map(fn(string $line): array => explode(' ', $line), $content);
        foreach ($linesOfNumbers as $key => $lineOfNumbers) {
            $linesOfNumbers[$key] = array_map(fn(string $number): int => (int) $number, $lineOfNumbers);
        }

        $results = [];
        foreach ($linesOfNumbers as $lineOfNumbers) {
            $layers = [$lineOfNumbers];
            $last = $lineOfNumbers;
            do {
                $forelast = $last;
                $last = [];
                for ($x = 0, $max = count($forelast) - 1; $x < $max; $x++) {
                    $last[] = $forelast[$x + 1] - $forelast[$x];
                }
                $layers[] = $last;
            } while (count(array_unique($last)) !== 1);

            $previous = 0;
            for ($depth = count($layers) -1; $depth >= 0; $depth--) {
                $first = $layers[$depth][array_key_first($layers[$depth])];
                $previous = $first - $previous;
                array_unshift($layers[$depth], $previous);
            }

            $results[] = ['layers' => $layers, 'previous' => $layers[0][array_key_first($layers[0])]];

        }

        return (string) array_sum(array_column($results, 'previous'));
    }
}