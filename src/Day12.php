<?php

declare(strict_types=1);

namespace Dannyvdsluijs\AdventOfCode2023;

use Dannyvdsluijs\AdventOfCode2023\Concerns\ContentReader;

class Day12
{
    use ContentReader;

    private $debug = false;
    private array $cache = [];

    public function partOne(): string
    {
        $lines = $this->readInputAsLines();
        $result = 0;

        foreach ($lines as $line) {
            [$pattern, $groups] = explode(' ', $line);
            $groups = array_map(intval(...), explode(',', $groups));

            $result += $this->calc($pattern, $groups);
            $this->debug && print(str_repeat('-', 10) . PHP_EOL);
        }

        return (string) $result;
    }

    public function partTwo(): string
    {
        $lines = $this->readInputAsLines();
        $result = 0;

        foreach ($lines as $line) {
            [$pattern, $groups] = explode(' ', $line);
            $groups = array_map(intval(...), explode(',', $groups));
            $groups = [...$groups, ...$groups, ...$groups, ...$groups, ...$groups];

            $result += $this->calc(substr(str_repeat($pattern . '?', 5), 0, -1), $groups);
            $this->debug && print(str_repeat('-', 10) . PHP_EOL);
        }

        return (string) $result;
    }

    /**
     * Since I was unable to build a performant enough algorithm (for part two) I've lookd into how others have solved this
     * Below is based on https://www.reddit.com/r/adventofcode/comments/18hbbxe/2023_day_12python_stepbystep_tutorial_with_bonus/
     *
     * This approach differs from mine in that it was detecting per group when it detects a first pound sign. I was processing per character.
     */
    private function calc(string $record, array $groups): int
    {
        $cacheKey = sprintf('%s-%s', $record, implode(',', $groups));
        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        if ($groups === []) {
            if (!str_contains($record, '#')) {
                return 1;
            }

            return 0;
        }

        if ($record === '') {
            return 0;
        }

        $out = match ($record[0]) {
            '#' => $this->pound($record, $groups),
            '.' => $this->dot($record, $groups),
            '?' => $this->pound($record, $groups) + $this->dot($record, $groups),
        };

        $this->debug && printf("'%s' %s -> %d" . PHP_EOL, $record, json_encode(($groups), JSON_THROW_ON_ERROR), $out);

        $this->cache[$cacheKey] = $out;

        return $out;
    }

    private function pound(string $record, array $groups): int
    {
        $nextGroup = $groups[0];
        $thisGroup = substr($record, 0, $nextGroup);
        $thisGroup = str_replace('?', '#', $thisGroup);

        if ($thisGroup !== str_repeat('#', $nextGroup)) {
            return 0;
        }

        if (strlen($record) === $nextGroup) {
            if (count($groups) === 1) {
                return 1;
            }

            return 0;
        }

        if (str_contains('.?', $record[$nextGroup])) {
            array_shift($groups);
            return $this->calc(substr($record, $nextGroup + 1), $groups);
        }
        return 0;
    }

    private function dot(string $record, array $groups): int
    {
        return $this->calc(substr($record, 1), $groups);
    }
}