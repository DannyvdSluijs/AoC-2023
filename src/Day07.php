<?php

declare(strict_types=1);

namespace Dannyvdsluijs\AdventOfCode2023;

use Dannyvdsluijs\AdventOfCode2023\Concerns\ContentReader;

class Day07
{
    const FIVE_OF_A_KIND = 10;
    const FOUR_OF_A_KIND = 9;
    const FULL_HOUSE = 8;
    const THREE_OF_A_KIND = 7;
    const TWO_PAIR = 6;
    const ONE_PAIR = 5;
    const HIGH_CARD = 4;

    use ContentReader;

    public function partOne(): string
    {
        $lines = $this->readInputAsLines();

        $hands = array_map(function(string $line) {
            [$cardsAsString, $bid] = explode(' ', $line);

            $cards = str_split($cardsAsString);
            $counts = array_count_values($cards);
            $arrayValues = array_values($counts);
            rsort($arrayValues);

            $hand = match ($arrayValues) {
                [5] => self::FIVE_OF_A_KIND,
                [4, 1] => self::FOUR_OF_A_KIND,
                [3, 2] => self::FULL_HOUSE,
                [3, 1, 1] => self::THREE_OF_A_KIND,
                [2, 2, 1] => self::TWO_PAIR,
                [2, 1, 1, 1] => self::ONE_PAIR,
                [1, 1, 1, 1, 1] => self::HIGH_CARD,
                default => throw new \Exception($cardsAsString)
            };

            return [
                'cardsAsString' => $cardsAsString,
                'cards' => $cards,
                'counts' => $counts,
                'hand' => $hand,
                'bid' => (int) $bid,
            ];
        }, $lines);

        usort($hands, function($a, $b): int {
            if ($a['hand'] !== $b['hand']) {
                return $a['hand'] <=> $b['hand'];
            }

            for($x = 0; $x <= 5; $x++) {
                if ($a['cards'][$x] !== $b['cards'][$x]) {
                    return $this->rankCard($b['cards'][$x]) <=> $this->rankCard($a['cards'][$x]);
                }
            }

            return 0;
        });

        $totalWinnings = [];
        foreach ($hands as $key => $hand) {
            $totalWinnings[] = $hand['bid'] * ($key + 1);
    }

        return (string) array_sum($totalWinnings);
    }

    public function partTwo(): string
    {
        $lines = $this->readInputAsLines();

        $hands = array_map(static function(string $line) {
            [$cardsAsString, $bid] = explode(' ', $line);

            $cards = str_split($cardsAsString);
            $counts = array_count_values($cards);
            $jCount = $counts['J'] ?? 0;
            $arrayValues = array_values($counts);
            rsort($arrayValues);

            switch ($arrayValues) {
                case [5]:
                    $hand = self::FIVE_OF_A_KIND;
                    break;
                case [4, 1]:
                    switch($jCount) {
                        case 1:
                        case 4:
                            $hand = self::FIVE_OF_A_KIND;
                            break;
                        default:
                            $hand = self::FOUR_OF_A_KIND;
                            break;
                    }
                    break;
                case [3, 2]:
                    switch($jCount) {
                        case 2:
                        case 3:
                            $hand = self::FIVE_OF_A_KIND;
                            break;
                        default:
                            $hand = self::FULL_HOUSE;
                            break;
                    }
                    break;
                case [3, 1, 1]:
                    switch($jCount) {
                        case 3:
                        case 1:
                            $hand = self::FOUR_OF_A_KIND;
                            break;
                        default:
                            $hand = self::THREE_OF_A_KIND;
                            break;
                    }
                    break;
                case [2, 2, 1]:
                    switch($jCount) {
                        case 2:
                            $hand = self::FOUR_OF_A_KIND;
                            break;
                        case 1:
                            $hand = self::FULL_HOUSE;
                            break;
                        default:
                            $hand = self::TWO_PAIR;
                            break;
                    }
                    break;
                case [2, 1, 1, 1]:
                    switch($jCount) {
                        case 1:
                        case 2:
                            $hand = self::THREE_OF_A_KIND;
                            break;
                        default:
                            $hand = self::ONE_PAIR;
                            break;
                    }
                    break;
                case [1, 1, 1, 1, 1]:
                    switch($jCount) {
                        case 1:
                            $hand = self::ONE_PAIR;
                            break;
                        default:
                            $hand = self::HIGH_CARD;
                            break;
                    }
                    break;
                default:
                    throw new \Exception($cardsAsString);
            }

            return [
                'cardsAsString' => $cardsAsString,
                'cards' => $cards,
                'counts' => $counts,
                'jCount' => $jCount,
                'hand' => $hand,
                'bid' => (int) $bid,
            ];
        }, $lines);

        usort($hands, function($a, $b): int {
            if ($a['hand'] !== $b['hand']) {
                return $a['hand'] <=> $b['hand'];
            }

            for($x = 0; $x <= 5; $x++) {
                if ($a['cards'][$x] !== $b['cards'][$x]) {
                    return $this->rankCard($b['cards'][$x], dayTwo: true) <=> $this->rankCard($a['cards'][$x], dayTwo: true);
                }
            }

            return 0;
        });

        $totalWinnings = [];
        foreach ($hands as $key => $hand) {
            $totalWinnings[] = $hand['bid'] * ($key + 1);
        }

        return (string) array_sum($totalWinnings);
    }

    private function rankCard(string $card, bool $dayTwo = false): int {
        if ($dayTwo && $card === 'J') {
            return 14;
        }
        return match ($card) {
            'A' => 1,
            'K' => 2,
            'Q' => 3,
            'J' => 4,
            'T' => 5,
            '9' => 6,
            '8' => 7,
            '7' => 8,
            '6' => 9,
            '5' => 10,
            '4' => 11,
            '3' => 12,
            '2' => 13,
        };
    }
}
