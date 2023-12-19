<?php

declare(strict_types=1);

namespace Dannyvdsluijs\AdventOfCode2023;

use Dannyvdsluijs\AdventOfCode2023\Concerns\ContentReader;

class Day19
{
    use ContentReader;

    public function partOne(): string
    {
        [$workflows, $ratings] = $this->parseInput();

        $accepted = [];
        foreach ($ratings as $rating) {
            $currentWorkflow = $workflows['in'];
            while (true) {
                foreach ($currentWorkflow['rules'] as $rule) {
                    switch ($rule['type']) {
                        case 'reject':
                            break 3;
                        case 'accept':
                            $accepted[] = $rating;
                            break 3;
                        case 'greaterThan':
                            if ($rating[$rule['param']] > $rule['threshold']) {
                                $currentWorkflow = $workflows[$rule['toWorkflow']];
                                break 2;
                            }
                            break;
                        case 'lessThan':
                            if ($rating[$rule['param']] < $rule['threshold']) {
                                $currentWorkflow = $workflows[$rule['toWorkflow']];
                                break 2;
                            }
                            break;
                        case 'toWorkflow':
                            $currentWorkflow = $workflows[$rule['toWorkflow']];
                            break 2;
                    }
                }
            }
        }

        $sums = array_map(array_sum(...), $accepted);
        return (string) array_sum($sums);
    }

    public function partTwo(): string
    {
        [$workflows] = $this->parseInput();

        $total = 0;
        $min = 1;
        $max= 4000;
        $constraints = [
            'workflow' => 'in',
            'workflowPath' => [],
            'x' => ['min' => $min, 'max' => $max],
            'm' => ['min' => $min, 'max' => $max],
            'a' => ['min' => $min, 'max' => $max],
            's' => ['min' => $min, 'max' => $max],
        ];
        $this->recursive($workflows, $constraints, $total);

        return (string) $total;
    }

    public function parseInput(): array
    {
        [$workflows, $ratings] = explode("\n\n", $this->readInput());

        $workflows = array_map(function (string $workflow): array {
            $workflow = str_replace(['{', '}'], ',', $workflow);
            $parts = explode(',', $workflow);
            array_pop($parts);

            $name = array_shift($parts);
            $rules = array_map(
                function (string $rule) {
                    if ($rule === 'R') {
                        return ['type' => 'reject'];
                    }
                    if ($rule === 'A') {
                        return ['type' => 'accept'];
                    }

                    $isGreaterThan = str_contains($rule, '>');
                    $rule = str_replace(['<', '>', ':'], ',', $rule);
                    $parts = explode(',', $rule);
                    if (count($parts) === 1) {
                        return [
                            'type' => 'toWorkflow',
                            'toWorkflow' => array_shift($parts),
                        ];
                    }
                    return [
                        'type' => $isGreaterThan ? 'greaterThan' : 'lessThan',
                        'param' => array_shift($parts),
                        'threshold' => (int)array_shift($parts),
                        'toWorkflow' => array_shift($parts),
                    ];
                },
                $parts
            );

            return [
                'name' => $name,
                'rules' => $rules,
            ];
        }, explode("\n", $workflows));

        $workflows = array_combine(
            array_column($workflows, 'name'),
            $workflows
        );
        $workflows['A'] = ['name' => 'A', 'rules' => [['type' => 'accept']]];
        $workflows['R'] = ['name' => 'B', 'rules' => [['type' => 'reject']]];

        $ratings = array_map(function (string $rating) {
            $rating = explode(',', str_replace('=', ',', substr($rating, 1, -1)));

            return [
                'x' => (int)$rating[1],
                'm' => (int)$rating[3],
                'a' => (int)$rating[5],
                's' => (int)$rating[7],
            ];
        }, explode("\n", $ratings));
        return array($workflows, $ratings);
    }

    private function recursive($workflows, array $workload, int &$result): void
    {
        $currentWorkflow = $workflows[$workload['workflow']];
        foreach ($currentWorkflow['rules'] as $rule) {
            switch ($rule['type']) {
                case 'reject':
                    break;
                case 'accept':
                    $ranges = [
                        'x' => $workload['x']['max'] - $workload['x']['min'] + 1,
                        'm' => $workload['m']['max'] - $workload['m']['min'] + 1,
                        'a' => $workload['a']['max'] - $workload['a']['min'] + 1,
                        's' => $workload['s']['max'] - $workload['s']['min'] + 1,
                    ];
                    $negatives = array_filter($ranges, fn (int $r) => $r < 1);
                    if ($negatives === []) {
                        $result += array_product($ranges);
                    }
                    break;
                case 'greaterThan':
                    $clone = $workload;
                    $clone['workflowPath'][] = $clone['workflow'];
                    $clone[$rule['param']]['min'] = $rule['threshold'] + 1;
                    $clone['workflow'] = $rule['toWorkflow'];
                    $this->recursive($workflows, $clone, $result);

                    // Next rule needs adjusted workload
                    $workload[$rule['param']]['max'] = $rule['threshold'];
                    break;
                case 'lessThan':
                    $clone = $workload;
                    $clone['workflowPath'][] = $clone['workflow'];
                    $clone[$rule['param']]['max'] = $rule['threshold'] - 1;
                    $clone['workflow'] = $rule['toWorkflow'];
                    $this->recursive($workflows, $clone, $result);

                    // Next rule needs adjusted workload
                    $workload[$rule['param']]['min'] = $rule['threshold'];
                    break;
                case 'toWorkflow':
                    $clone = $workload;
                    $clone['workflowPath'][] = $clone['workflow'];
                    $clone['workflow'] = $rule['toWorkflow'];
                    $this->recursive($workflows, $clone, $result);
                    break;
            }
        }
    }
}