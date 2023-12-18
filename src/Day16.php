<?php

declare(strict_types=1);

namespace Dannyvdsluijs\AdventOfCode2023;

use Dannyvdsluijs\AdventOfCode2023\Concerns\ContentReader;

class Day16
{
    use ContentReader;

    public function partOne(): string
    {
        $grid = $this->readInputAsGridOfCharacters();
        $beams = [
            ['start' => [0,0], 'startHeading' => 'east', 'pos' => [0, 0], 'heading' => 'east', 'split' => false]
        ];

        return (string) $this->computeEnergy($grid, $beams);
    }

    public function partTwo(): string
    {
        $grid = $this->readInputAsGridOfCharacters();
        $depth = count($grid);
        $width = count($grid[0]);

        $results = [];
        for ($x = 0; $x < $depth; $x++) {
            $results[] = $this->computeEnergy($grid, [['start' => [$x,0], 'startHeading' => 'east', 'pos' => [$x, 0], 'heading' => 'east', 'split' => false]]);
            $results[] = $this->computeEnergy($grid, [['start' => [$x,$width-1], 'startHeading' => 'west', 'pos' => [$x, $width-1], 'heading' => 'west', 'split' => false]]);
        }
        for ($y = 0; $y < $width; $y++) {
            $results[] = $this->computeEnergy($grid, [['start' => [0,$y], 'startHeading' => 'south', 'pos' => [0,$y], 'heading' => 'south', 'split' => false]]);
            $results[] = $this->computeEnergy($grid, [['start' => [$depth-1, $y], 'startHeading' => 'north', 'pos' => [$depth-1,$y], 'heading' => 'north', 'split' => false]]);
        }

        return (string) max($results);
    }

    private function north(array $pos): array
    {
        return [$pos[0] - 1, $pos[1]];
    }

    private function east(array $pos): array
    {
        return [$pos[0], $pos[1] + 1];
    }

    private function south(array $pos): array
    {
        return [$pos[0] + 1, $pos[1]];
    }

    private function west(array $pos): array
    {
        return [$pos[0], $pos[1] - 1];
    }

    private function hasBeam(array $beams, array $pos, string $startHeading): bool
    {
        return array_filter(
            $beams,
            static fn (array $beam) => $beam['start'] === $pos && $beam['startHeading'] === $startHeading
            ) !== [];
    }

    private function computeEnergy(array $grid, array $beams): int
    {
        $energized = [];
        $unsplitBeams = array_filter($beams, static fn(array $beam) => $beam['split'] === false);
        while ($unsplitBeams !== []) {
            // Validate beams are unique
            $beamMap = array_map(fn(array $b) => implode(',', [$b['start'][0], $b['start'][1] , $b['startHeading']]), $beams);
            $duplicates = array_filter(array_count_values($beamMap), fn ($v, $k) => $v > 1, ARRAY_FILTER_USE_BOTH);
            if ($duplicates !== []) {
                var_dump(array_keys($duplicates));
                throw new \Exception('Duplicates beams');
            }

            foreach ($unsplitBeams as $key => $unsplitBeam) {
                while ($beams[$key]['split'] === false) {
                    // @todo off grid protection
                    $current = $grid[$beams[$key]['pos'][0]][$beams[$key]['pos'][1]] ?? null;

                    if (\is_null($current)) {
                        $beams[$key]['split'] = true; // Abuse split when running offgrid
                        break;
                    }

                    if ($current === '.') {
                        $energized[] = implode(',', $beams[$key]['pos']);
                        $beams[$key]['pos'] = match ($beams[$key]['heading']) {
                            'east' => $this->east($beams[$key]['pos']),
                            'south' => $this->south($beams[$key]['pos']),
                            'west' => $this->west($beams[$key]['pos']),
                            'north' => $this->north($beams[$key]['pos']),
                        };
                        continue;
                    }

                    if ($current === '/') {
                        $energized[] = implode(',', $beams[$key]['pos']);
                        $beams[$key]['heading'] = match ($beams[$key]['heading']) {
                            'east' => 'north',
                            'south' => 'west',
                            'west' => 'south',
                            'north' => 'east',
                        };
                        $beams[$key]['pos'] = match ($beams[$key]['heading']) {
                            'east' => $this->east($beams[$key]['pos']),
                            'south' => $this->south($beams[$key]['pos']),
                            'west' => $this->west($beams[$key]['pos']),
                            'north' => $this->north($beams[$key]['pos']),
                        };
                        continue;
                    }

                    if ($current === '\\') {
                        $energized[] = implode(',', $beams[$key]['pos']);
                        $beams[$key]['heading'] = match ($beams[$key]['heading']) {
                            'east' => 'south',
                            'south' => 'east',
                            'west' => 'north',
                            'north' => 'west',
                        };
                        $beams[$key]['pos'] = match ($beams[$key]['heading']) {
                            'east' => $this->east($beams[$key]['pos']),
                            'south' => $this->south($beams[$key]['pos']),
                            'west' => $this->west($beams[$key]['pos']),
                            'north' => $this->north($beams[$key]['pos']),
                        };
                        continue;
                    }

                    if ($current === '|') {
                        $energized[] = implode(',', $beams[$key]['pos']);

                        // North and south end on the pointy bit as if it was an empty space
                        if (in_array($beams[$key]['heading'], ['north', 'south'])) {
                            $beams[$key]['pos'] = match ($beams[$key]['heading']) {
                                'east' => $this->east($beams[$key]['pos']),
                                'south' => $this->south($beams[$key]['pos']),
                                'west' => $this->west($beams[$key]['pos']),
                                'north' => $this->north($beams[$key]['pos']),
                            };
                            continue;
                        }

                        // Create new beams
                        if (!$this->hasBeam($beams, $beams[$key]['pos'], 'north')) {
                            $beams[] = ['start' => $beams[$key]['pos'], 'startHeading' => 'north', 'pos' => $this->north($beams[$key]['pos']), 'heading' => 'north', 'split' => false];
                        }
                        if (!$this->hasBeam($beams, $beams[$key]['pos'], 'south')) {
                            $beams[] = ['start' => $beams[$key]['pos'], 'startHeading' => 'south', 'pos' => $this->south($beams[$key]['pos']), 'heading' => 'south', 'split' => false];
                        }
                        $beams[$key]['split'] = true;
                    }

                    if ($current === '-') {
                        $energized[] = implode(',', $beams[$key]['pos']);

                        // East and west end on the pointy bit as if it was an empty space
                        if (in_array($beams[$key]['heading'], ['east', 'west'])) {
                            $beams[$key]['pos'] = match ($beams[$key]['heading']) {
                                'east' => $this->east($beams[$key]['pos']),
                                'south' => $this->south($beams[$key]['pos']),
                                'west' => $this->west($beams[$key]['pos']),
                                'north' => $this->north($beams[$key]['pos']),
                            };
                            continue;
                        }

                        // Create new beams
                        if (!$this->hasBeam($beams, $beams[$key]['pos'], 'east')) {
                            $beams[] = ['start' => $beams[$key]['pos'], 'startHeading' => 'east', 'pos' => $this->east($beams[$key]['pos']), 'heading' => 'east', 'split' => false];
                        }
                        if (!$this->hasBeam($beams, $beams[$key]['pos'], 'west')) {
                            $beams[] = ['start' => $beams[$key]['pos'], 'startHeading' => 'west', 'pos' => $this->west($beams[$key]['pos']), 'heading' => 'west', 'split' => false];
                        }
                        $beams[$key]['split'] = true;
                    }
                }
            }
            $unsplitBeams = array_filter(
                $beams,
                static fn(array $beam) => $beam['split'] === false
            );
        }

        return count(array_unique($energized));
    }
}