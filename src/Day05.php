<?php

declare(strict_types=1);

namespace Dannyvdsluijs\AdventOfCode2023;

use Dannyvdsluijs\AdventOfCode2023\Concerns\ContentReader;

class Day05
{
    use ContentReader;

    public function partOne(): string
    {
        [$seeds, $map, $steps] = $this->parseInput();

        foreach ($seeds as $key => $seed) {
            foreach ($steps as $previous => $current) {
                if ($previous === 0) {
                    continue;
                }

                $mappingName = sprintf('%s-to-%s', $previous, $current);
                $mapping = $map[$mappingName];

                foreach ($mapping as $m) {
                    if ($seed >= $m['sourceRangeStart'] && $seed < $m['sourceRangeStart'] + $m['rangeLength']) {
                        $offset = $seed - $m['sourceRangeStart'];
                        $seeds[$key] = $seed = $m['destinationRangeStart'] + $offset;
                        continue 2;
                    }
                }
            }
        }


        return (string) min($seeds);
    }

    public function partTwo(): string
    {
        [$ranges, $mappings, $steps] = $this->parseInput();
        $ranges = array_chunk($ranges, 2);

        foreach ($steps as $previous => $current) {
            if ($previous === 0) {
                continue;
            }

            $mappingName = sprintf('%s-to-%s', $previous, $current);
            $mapping = $mappings[$mappingName];

            $newRanges = [];
            foreach ($ranges as $range) {
                [$start, $length] = $range;
                $newRanges[] = $this->remapRange($start, $length, $mapping);
            }

            $ranges = array_merge(...$newRanges);
        }

        return (string) min(array_column($ranges, 0));
    }

    private function parseInput(): array
    {
        $content = $this->readInput();
        $blocks = explode("\n\n", $content);
        [, $seeds] = explode(':', array_shift($blocks));
        $seeds = array_map(intval(...), explode(' ', trim($seeds)));

        $mappings = [];
        foreach ($blocks as $block) {
            [$name, $maps] = explode(':', $block);
            $name = substr($name, 0, -4);
            $mappings[$name] = [];
            foreach (explode("\n", trim($maps)) as $m) {
                [$destinationRangeStart, $sourceRangeStart, $rangeLength] = explode(' ', $m);
                $mappings[$name][] = [
                    'destinationRangeStart' => (int)$destinationRangeStart,
                    'sourceRangeStart' => (int)$sourceRangeStart,
                    'rangeLength' => (int)$rangeLength,
                ];
            }
            usort($mappings[$name], static fn($a, $b) => $a['sourceRangeStart'] <=> $b['sourceRangeStart']);
        }

        $possibleMappings = array_keys($mappings);
        $steps = [0 => 'seed'];

        $search = 'seed';
        while (true) {
            $matches = array_filter($possibleMappings, static fn($mapping) => str_starts_with($mapping, $search));
            if ($matches === []) {
                break;
            }
            $match = array_shift($matches);
            [, ,$next] = explode('-', $match);
            $steps[$search] = $next;
            $search = $next;
        }

        return [$seeds, $mappings, $steps];
    }

    private function remapRange($start, $length, $mappings): array
    {
        $end = $start + $length - 1;
        $remaps = [];
        foreach ($mappings as $mapping) {
            $startBeforeMapping = $start < $mapping['sourceRangeStart'];
            $startInMapping = $start >= $mapping['sourceRangeStart'] && $start <= $mapping['sourceRangeStart'] + $mapping['rangeLength'] - 1;
            $startAfterMapping = $start > $mapping['sourceRangeStart'] + $mapping['rangeLength'] - 1;
            $endBeforeMapping = $end < $mapping['sourceRangeStart'];
            $endInMapping = $end >= $mapping['sourceRangeStart'] && $end <= $mapping['sourceRangeStart'] + $mapping['rangeLength'] - 1;
            $endAfterMapping = $end > $mapping['sourceRangeStart'] + $mapping['rangeLength'] - 1;

            // A: Completely before
            if ($endBeforeMapping) {
                return [[$start, $length]];
            }
            // B: Start is before and End is in the mapping window
            if ($startBeforeMapping && $endInMapping) {
                $lengthBefore = $mapping['sourceRangeStart'] - $start;
                $remaps[] = [$start, $lengthBefore];
                $remaps[] = [$mapping['destinationRangeStart'], $length - $lengthBefore];
                return $remaps;
            }
            // C:Start is in the mapping window and End is outside
            if ($startInMapping && $endAfterMapping) {
                $offset = $start - $mapping['sourceRangeStart'];
                $lengthInside = $mapping['sourceRangeStart'] + $mapping['rangeLength'] - $start;
                $remaps[] = [[$mapping['destinationRangeStart'] + $offset, $lengthInside]];
                $remaps[] = $this->remapRange($start + $lengthInside, $length - $lengthInside, $mappings);
                return array_merge(...$remaps);
            }
            // D: Both start and end are inside mapping window
            if ($startInMapping && $endInMapping) {
                $offset = $start - $mapping['sourceRangeStart'];
                $remaps[] = [$mapping['destinationRangeStart'] + $offset, $length];
                return $remaps;
            }
            // E: Start is before and End is after mapping window
            if ($startBeforeMapping && $endAfterMapping) {
                $lengthBefore = $mapping['sourceRangeStart'] - $start;
                $remaps[] = [$start, $lengthBefore];
                $remaps[] = [$mapping['destinationRangeStart'], $mapping['rangeLength']];
                $remaps[] = $this->remapRange($start + + $lengthBefore + $mapping['rangeLength'], $length - $lengthBefore - $mapping['rangeLength'] - 1, $mappings);
                return array_merge(...$remaps);
            }
            // F: Range is completely after mapping, leave it to the loop.

        }

        return [[$start, $length]];
    }
}