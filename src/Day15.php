<?php

declare(strict_types=1);

namespace Dannyvdsluijs\AdventOfCode2023;

use Dannyvdsluijs\AdventOfCode2023\Concerns\ContentReader;

class Day15
{
    use ContentReader;

    public function partOne(): string
    {
        $content = $this->readInput();
        $strings = explode(',', $content);

        $results = [];
        foreach ($strings as $string) {
            $currentValue = 0;
            $chars = str_split($string);
            foreach ($chars as $char) {
                $currentValue += ord($char);
                $currentValue *= 17;
                $currentValue %= 256;
            }

            $results[] = $currentValue;
        }

        return (string) array_sum($results);
    }

    public function partTwo(): string
    {
        $content = $this->readInput();
        $boxes = array_fill(0, 256, []);
        $strings = explode(',', $content);

        foreach ($strings as $string) {
            if (str_ends_with($string, '-')) {
                $label = substr($string, 0, -1);
                $hash = $this->hash($label);
                $boxes[$hash] = array_values(array_filter($boxes[$hash], fn(array $lens) => $lens['label'] !== $label));
                continue;
            }

            $label = substr($string, 0, -2);
            $hash = $this->hash($label);
            $focal = substr($string, -1);
            $matches = array_filter($boxes[$hash], fn(array $lens) => $lens['label'] === $label);

            if ($matches === []) {
                $boxes[$hash][] = ['label' => $label, 'focal' => $focal];
                continue;
            }

            $matchKey = array_key_first($matches);
            $boxes[$hash][$matchKey]['focal'] = $focal;
        }

        $results = [];
        foreach ($boxes as $boxNumber => $box) {
            foreach ($box as $lensNumber => $lens) {
                $results[] = ($boxNumber + 1) * ($lensNumber  + 1) * $lens['focal'];
            }
        }
        return (string) array_sum($results);
    }

    private function hash(string $input): int
    {
        $currentValue = 0;
        $chars = str_split($input);

        foreach ($chars as $char) {
            $currentValue += ord($char);
            $currentValue *= 17;
            $currentValue %= 256;
        }

        return $currentValue;
    }
}