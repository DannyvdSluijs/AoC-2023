<?php

declare(strict_types=1);

namespace Dannyvdsluijs\AdventOfCode2023;

use Dannyvdsluijs\AdventOfCode2023\Concerns\ContentReader;

class Day12
{
    use ContentReader;

    public function partOne(): string
    {
        $lines = $this->readInputAsLines();
        $result = [];

        foreach ($lines as $line) {
            [$conditions, $groups] = explode(' ', $line);
            $conditions = str_split($conditions);
            $groups = array_map(intval(...), explode(',', $groups));

            $result[] = $this->findValidArrangements($conditions, $groups);
        }

        return (string) array_sum($result);
    }

    public function partTwo(): string
    {
        $lines = $this->readInputAsLines();
        $result = [];
        $lineCount = count($lines);
        foreach ($lines as $k => $line) {
            printf("%d / %d\r\n", $k +1, $lineCount);
            [$conditions, $groups] = explode(' ', $line);
            $conditions = str_split(substr(str_repeat($conditions . '?', 5), 0, -1));
            $groups = array_map(intval(...), explode(',', $groups));
            $groups = [...$groups, ...$groups, ...$groups, ...$groups, ...$groups,];

            $result[] = $this->findValidArrangements($conditions, $groups);
        }

        return (string) array_sum($result);
    }

    private function findValidArrangements(array $conditions, array $groups, array $context = null): int
    {
        if (\is_null($context)) {
            $context = [
                'unknowns' => array_keys(array_filter($conditions, fn(string $char) => $char === '?')),
                'unknownsCount' => count(array_filter($conditions, fn(string $char) => $char === '?')),
                'maxHashes' => array_sum($groups),
                'hashCount' => count(array_filter($conditions, fn(string $char) => $char === '#')),
                'groups' => [],
                'currentGroup' => ['type' => null, 'count' => 0],
                'hashPattern' => [],
                'hashGroupsCount' => 0,
                'pos' => 0,
                'length' => count($conditions),
                'last' => $conditions[array_key_first($conditions)],
                'path' => [],
            ];
        }

        // Pre flight checks
        if ($context['hashCount'] > $context['maxHashes']) {
            return 0; // Hash count is over desired hash count
        }
        if ($context['hashCount'] + $context['unknownsCount'] < $context['maxHashes']) {
            return 0; // Net enough unknowns to reach desired hash count
        }
        if ($context['hashPattern'] !== []) {
            if (array_slice($groups, 0, $context['hashGroupsCount']) !== $context['hashPattern']) {
                return 0;
            }
        }

        for (; $context['pos'] < $context['length']; $context['pos']++) {
            $current = $conditions[$context['pos']];
            $context['last'] = $context['path'][$context['pos'] - 1] ?? '';

            if ($current === '?') {
                // Dot
                $dotContext = $context;
                array_shift($dotContext['unknowns']);
                $dotContext['unknownsCount'] -= 1;
                if ($dotContext['currentGroup']['type'] === '.') {
                    $dotContext['currentGroup']['count'] += 1;
                } else {
                    if ($dotContext['currentGroup']['type'] === '#') {
                        $dotContext['hashPattern'][] = $dotContext['currentGroup']['count'];
                        $dotContext['hashGroupsCount'] += 1;
                    }
                    $dotContext['groups'][] = $dotContext['currentGroup'];
                    $dotContext['currentGroup'] = ['type' => '.', 'count' => 1];
                }
                $dotContext['pos'] += 1;
                $dotContext['path'][] = '.';
                $dotResult = $this->findValidArrangements($conditions, $groups, $dotContext);
                unset($dotContext);

                // Hash
                $hashContext = $context;
                array_shift($hashContext['unknowns']);
                $hashContext['unknownsCount'] -= 1;
                if ($hashContext['currentGroup']['type'] === '#') {
                    $hashContext['currentGroup']['count'] += 1;
                } else {
                    $hashContext['groups'][] = $hashContext['currentGroup'];
                    $hashContext['currentGroup'] = ['type' => '#', 'count' => 1];
                }
                $hashContext['pos'] += 1;
                $hashContext['hashCount'] += 1;
                $hashContext['path'][] = '#';
                $hashResult = $this->findValidArrangements($conditions, $groups, $hashContext);
                unset($hashContext);

                return $dotResult + $hashResult;
            }

            $context['path'][] = $current;

            if ($current === $context['last']) {
                $context['currentGroup']['type'] = $current;
                $context['currentGroup']['count'] += 1;
            } else {
                if ($context['currentGroup']['type'] === '#') {
                    $context['hashPattern'][] = $context['currentGroup']['count'];
                    $context['hashGroupsCount'] += 1;
                }
                $context['groups'][] = $context['currentGroup'];
                $context['currentGroup'] = ['type' => $current, 'count' => 1];
            }
        }

        if ($context['currentGroup']['type'] === '#') {
            $context['hashPattern'][] = $context['currentGroup']['count'];
            $context['hashGroupsCount'] += 1;
        }

        return $context['hashPattern'] === $groups ? 1 : 0;
    }
}