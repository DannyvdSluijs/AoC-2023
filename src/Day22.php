<?php

declare(strict_types=1);

namespace Dannyvdsluijs\AdventOfCode2023;

use Dannyvdsluijs\AdventOfCode2023\Concerns\ContentReader;

class Day22
{
    use ContentReader;

    public function partOne(): string
    {
        $lines = $this->readInputAsLines();
        $coords = array_map($this->lineToBricks(...), $lines);
        $bricks = array_map($this->brickToBitMask(...), $coords);

        [$bricks] = $this->letBricksFallUntilStable($bricks);
        $supportingStructure = $this->computeSupportingStructure($bricks);
        $bricksThatDontSupportOtherBricks = $this->findBricksThatDontSupportOtherBricks($supportingStructure);

        return (string) count($bricksThatDontSupportOtherBricks);
    }

    public function partTwo(): string
    {
        $lines = $this->readInputAsLines();
        $coords = array_map($this->lineToBricks(...), $lines);
        $bricks = array_map($this->brickToBitMask(...), $coords);

        [$bricks] = $this->letBricksFallUntilStable($bricks);
        $supportingStructure = $this->computeSupportingStructure($bricks);
        $bricksThatDontSupportOtherBricks = $this->findBricksThatDontSupportOtherBricks($supportingStructure);

        $count = 0;
        foreach ($bricks as $brick) {
            $brickId = $brick[array_key_first($brick)]['brick']['id'];
            if (in_array($brickId, $bricksThatDontSupportOtherBricks, true)) {
                continue;
            }

            $fallenBricks = $this->findFallingBricks($brickId, $supportingStructure, [$brickId]);
            $count += count($fallenBricks) - 1;
        }

        // 34385 => That is not the right answer, to low
        return (string) $count;
    }

    private function lineToBricks(string $line): array
    {
        static $brick = 1;
        $line = str_replace('~', ',', $line);
        [$x1, $y1, $z1, $x2, $y2, $z2] = explode(',', $line);

        if ($z1 !== $z2 && ($x1 !== $x2 || $y1 !== $y2)) {
            throw new \RuntimeException('Found brick with h>1 && (w>1 || d>1)');
        }
        return [
            'id' => $brick++,
            'x1' => (int) $x1,
            'y1' => (int) $y1,
            'z1' => (int) $z1,
            'x2' => (int) $x2,
            'y2' => (int) $y2,
            'z2' => (int) $z2,
        ];
    }

    private function brickToBitMask(array $brick): array
    {
        $masks = [];
        for ($z = $brick['z1']; $z <= $brick['z2']; $z++) {
            $lowerBitmask = 0;
            $upperBitMask = 0;
            for ($x = $brick['x1']; $x <= $brick['x2']; $x++) {
                for ($y = $brick['y1']; $y <= $brick['y2']; $y++) {
                    $shifts = ($x * 10) + $y;
                    $upper = $shifts > 62;
                    $bit = 1 << ($upper ? $shifts - 62 : $shifts);
                    if ($upper) {
                        $upperBitMask += $bit;
                    } else {
                        $lowerBitmask += $bit;
                    }
                }
            }
            if ($lowerBitmask < 0 || $upperBitMask < 0) {
                throw new \RuntimeException('Either bitmask is below zero');
            }
            $masks[$z] = ['lowerBitmask' => $lowerBitmask, 'upperBitmask' => $upperBitMask, 'brick' => $brick];
        }

        return $masks;
    }

    private function letBricksFallUntilStable(array $bricks): array
    {
        printf("Settle bricks until stable\n");
        $count = count($bricks);
        $fallenBricks = 0;

        usort($bricks, static fn($a, $b) => array_key_first($a) <=> array_key_first($b));
        foreach ($bricks as $i => $brick) {
            if ($i % 100 === 0) {
                printf("Completed %d/%d bricks\n", $i, $count);
            }
            $currentBottomZ = array_key_first($brick);

            $bricksWithLowerZAndOverlappingBitmask = array_filter($bricks, function(array $b) use ($brick, $currentBottomZ) {
                $top = array_key_last($b);
                if ($top >= $currentBottomZ) {
                    return false;
                }

                return ($brick[$currentBottomZ]['lowerBitmask'] > 0  && ($b[$top]['lowerBitmask'] & $brick[$currentBottomZ]['lowerBitmask']) > 0) ||
                    ($brick[$currentBottomZ]['upperBitmask'] > 0  && ($b[$top]['upperBitmask'] & $brick[$currentBottomZ]['upperBitmask']) > 0);
            });

            $maxZOfBricksBelowWithOverlappingBitmask = 0;
            try {
                $maxZOfBricksBelowWithOverlappingBitmask = max(array_map(static fn($b) => array_key_last($b), $bricksWithLowerZAndOverlappingBitmask));
            } catch (\ValueError) {}

            if ($currentBottomZ === $maxZOfBricksBelowWithOverlappingBitmask + 1) {
                // Brick isn't falling
                continue;
            }

            $bricks[$i] = array_combine(
                range($maxZOfBricksBelowWithOverlappingBitmask + 1, $maxZOfBricksBelowWithOverlappingBitmask + count($brick)),
                array_values($brick)
            );
            $fallenBricks++;
        }

        return [$bricks, $fallenBricks];
    }

    private function computeSupportingStructure(array $bricks): array
    {
        printf("Compute supporting structure\n");
        $supportInfo = [];
        foreach ($bricks as $brick) {
            $bottomZCurrentBrick = array_key_first($brick);
            $bricksBelow = array_filter($bricks, static function($b) use ($brick, $bottomZCurrentBrick) {
                if (array_key_last($b) + 1 !== $bottomZCurrentBrick) {
                    return false;
                }

                return ($brick[$bottomZCurrentBrick]['lowerBitmask'] > 0  && ($b[$bottomZCurrentBrick - 1]['lowerBitmask'] & $brick[$bottomZCurrentBrick]['lowerBitmask']) > 0) ||
                    ($brick[$bottomZCurrentBrick]['upperBitmask'] > 0  && ($b[$bottomZCurrentBrick - 1]['upperBitmask'] & $brick[$bottomZCurrentBrick]['upperBitmask']) > 0);
            });
            $bricksBelow = array_map(static fn($b) => $b[$bottomZCurrentBrick - 1]['brick']['id'], $bricksBelow);

            $topZCurrentBrick = array_key_last($brick);
            $bricksAbove = array_filter($bricks, static function($b) use ($brick, $topZCurrentBrick) {
                if (array_key_first($b) - 1 !== $topZCurrentBrick) {
                    return false;
                }

                return ($brick[$topZCurrentBrick]['lowerBitmask'] > 0  && ($b[$topZCurrentBrick + 1]['lowerBitmask'] & $brick[$topZCurrentBrick]['lowerBitmask']) > 0) ||
                    ($brick[$topZCurrentBrick]['upperBitmask'] > 0  && ($b[$topZCurrentBrick + 1]['upperBitmask'] & $brick[$topZCurrentBrick]['upperBitmask']) > 0);
            });
            $bricksAbove = array_map(static fn($b) => $b[$topZCurrentBrick + 1]['brick']['id'], $bricksAbove);

            $supportInfo[$brick[$bottomZCurrentBrick]['brick']['id']] = ['bricksBelow' => array_values($bricksBelow), 'bricksAbove' => array_values($bricksAbove)];
        }

        return $supportInfo;
    }

    private function findBricksThatDontSupportOtherBricks(array $supportInfo): array
    {
        printf("Count disintegrate options\n");
        // See if we can disintegrate
        $disintegrate = [];
        foreach ($supportInfo as $brick => $s) {
            foreach ($s['bricksAbove'] as $brickAbove) {
                if (count($supportInfo[$brickAbove]['bricksBelow']) === 1) {
                    continue 2;
                }
            }

            $disintegrate[] = $brick;
        }

        return $disintegrate;
    }

    private function findFallingBricks(int $brickId, array $supportingStructure, array $alreadyFallen = []): array
    {
        $added = [];
        foreach ($supportingStructure[$brickId]['bricksAbove'] as $brickAbove) {
            $intersect = array_intersect($supportingStructure[$brickAbove]['bricksBelow'], $alreadyFallen);

            if ($intersect !== $supportingStructure[$brickAbove]['bricksBelow']) {
                continue;
            }

            $alreadyFallen[] = $brickAbove;
            $added[] = $brickAbove;
        }

        $recursiveResults = [];
        foreach ($added as $a) {
            $recursiveResults[] = $this->findFallingBricks($a, $supportingStructure, $alreadyFallen);
        }

        $alreadyFallen = array_merge($alreadyFallen, ...$recursiveResults);

        return array_unique($alreadyFallen);
    }

}