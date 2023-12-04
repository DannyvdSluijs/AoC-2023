<?php

declare(strict_types=1);

namespace Dannyvdsluijs\AdventOfCode2023;

use Dannyvdsluijs\AdventOfCode2023\Concerns\ContentReader;

class Day04
{
    use ContentReader;

    public function partOne(): string
    {
        $lines = $this->readInputAsLines();
        $points = 0;

        foreach ($lines as $line) {
            $line = str_replace([':', '  '], ['|', ' '], $line);
            [$game, $winningNumbers, $numberYouHave] = explode('|', $line);
            $winningNumbers = array_map(intval(...), explode(' ', trim($winningNumbers)));
            $numberYouHave = array_map(intval(...), explode(' ', trim($numberYouHave)));

            $matching = array_intersect($winningNumbers, $numberYouHave);
            if (count($matching) === 0) {
                continue;
            }
            $points += pow(2, (count($matching) - 1));

            echo $game . ': Matching: ' . count($matching) . '; '  . (pow(2, (count($matching) - 1))) . ' (total: ' . $points . ')' . PHP_EOL;
        }

        return (string) $points;
    }

    public function partTwo(): string
    {
        $lines = $this->readInputAsLines();
        $scratchCards = [];
        $points = 0;
        $scratched = 0;

        foreach ($lines as $line) {
            $line = str_replace([':', '  '], ['|', ' '], $line);
            [$game, $winningNumbers, $numberYouHave] = explode('|', $line);
            $game = (int) substr($game, 5);
            $winningNumbers = array_map(intval(...), explode(' ', trim($winningNumbers)));
            $numberYouHave = array_map(intval(...), explode(' ', trim($numberYouHave)));

            $scratchCards[$game] = [
                'amount' => 1,
                'winning' => $winningNumbers,
                'you' => $numberYouHave,
            ];
        }

        foreach ($scratchCards as $game => $card) {
            $card = $scratchCards[$game];
            $scratched += $card['amount'];
            $matching = array_intersect($card['winning'], $card['you']);
            $count = count($matching);
            if ($count === 0) {
                continue;
            }

            for ($x = 1; $x <= $count; $x++) {
                if (!array_key_exists($game + $x, $scratchCards)) {
                    continue;
                }

                $scratchCards[$game + $x]['amount'] = $scratchCards[$game + $x]['amount'] +  $card['amount'];
            }
        }

        return (string) $scratched;
    }
}