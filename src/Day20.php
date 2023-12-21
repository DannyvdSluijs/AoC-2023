<?php

declare(strict_types=1);

namespace Dannyvdsluijs\AdventOfCode2023;

use Dannyvdsluijs\AdventOfCode2023\Concerns\ContentReader;

class Day20
{
    use ContentReader;

    private const LOW = false;
    private const HIGH = true;

    public function partOne(): string
    {
        $modules = $this->parseInput();

        $counter = [self::LOW => 0, self::HIGH => 0];
        for ($x = 0; $x < 1_000; $x++) {
            $modules = $this->sendPulse($modules, $counter);
        }

        return (string) array_product($counter);
    }

    public function partTwo(): string
    {
        return '';
    }

    private function sendPulse(array $modules, array &$counter): array
    {
        $stack = [['broadcaster', self::LOW, 'button']];

        while ($stack !== []) {
            [$current, $signal, $source] = array_shift($stack);
            $counter[$signal] += 1;

            if (!array_key_exists($current, $modules)) {
                continue;
            }

            // FlipFlop works as a latch, update state and dispatch its own state as the upstream signal to each destination
            if ($modules[$current]['isFlipFlop']) {
                if ($signal === self::HIGH) {
                    continue;
                }
                $modules[$current]['state'] = !$modules[$current]['state'];

                foreach ($modules[$current]['outputs'] as $output) {
                    $stack[] = [$output, $modules[$current]['state'], $current];
                }
                continue;
            }

            // Inverter only inverts the signal and passes it down as state
            if ($modules[$current]['isConjunction']) {
                $modules[$current]['inputs'][$source] = $signal;
                $outputSignal = array_values(array_unique($modules[$current]['inputs'])) === [self::HIGH] ? self::LOW : self::HIGH;
                foreach ($modules[$current]['outputs'] as $output) {
                    $stack[] = [$output, $outputSignal, $current];
                }
                continue;
            }

            // Others just send the signal downstream
            foreach ($modules[$current]['outputs'] as $output) {
                $stack[] = [$output, $signal, $current];
            }
        }

        return $modules;
    }

    private function parseInput(): array
    {
        $lines = $this->readInputAsLines();
        $modules = array_map(function (string $line): array {
            $line = str_replace(' -> ', ',', $line);
            $parts = explode(',', $line);
            $parts = array_map(trim(...), $parts);

            $name = array_shift($parts);
            $isFlipFlop = str_starts_with($name, '%');
            $isConjunction = str_starts_with($name, '&');
            if ($isFlipFlop || $isConjunction) {
                $name = substr($name, 1);
            }

            return [
                'name' => $name,
                'isFlipFlop' => $isFlipFlop,
                'isConjunction' => $isConjunction,
                'inputs' => [],
                'outputs' => $parts,
                'state' => self::LOW
            ];
        }, $lines);
        $modules = array_combine(
            array_column($modules, 'name'),
            $modules,
        );
        $modules['output'] = [
            'name' => 'output',
            'isFlipFlop' => false,
            'isConjunction' => false,
            'inputs' => [],
            'outputs' => [],
            'state' => self::LOW,
        ];
        // Foreach conjunction find its inputs
        $conjunctions = array_filter($modules, fn(array $module): bool => $module['isConjunction']);
        foreach ($conjunctions as $moduleName => $value) {
            $inputs = array_filter($modules, fn(array $module): bool => in_array($moduleName, $module['outputs'], true));
            $inputs = array_map(fn() => self::LOW, $inputs);
            $modules[$moduleName]['inputs'] = $inputs;
        }
        return $modules;
    }
}