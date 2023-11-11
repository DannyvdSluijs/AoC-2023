<?php

declare(strict_types=1);

namespace Dannyvdsluijs\AdventOfCode2023;

class DayPreparation
{
    public function __construct(
        public readonly int $year,
        public readonly int $day,
    ) {
    }

    public function __invoke(): void
    {
        $session = $this->loadSessionFromFile();
        $output = $this->fetchInput($session);
        file_put_contents(sprintf('%s/../inputs/day%02d.txt', __DIR__, $this->day), $output);
        $this->prepareSolutionTemplate();
    }

    private function loadSessionFromFile(): string
    {
        $session = file_get_contents(sprintf('%s/../.session', __DIR__));
        if ($session === false) {
            throw new \RuntimeException('Unable to read from .session file');
        }
        return $session;
    }

    private function fetchInput(string $session): string|bool
    {
        $ch = curl_init(sprintf("https://adventofcode.com/%d/day/%d/input", $this->year, $this->day));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [sprintf('Cookie: session=%s', $session)]);
        return curl_exec($ch);
    }

    private function prepareSolutionTemplate(): void
    {
        $content = file_get_contents(sprintf('%s/DayXX.template', __DIR__));
        $content = str_replace('DayXX', sprintf("Day%02d", $this->day), $content);
        file_put_contents(sprintf('%s/Day%02d.php', __DIR__, $this->day), $content);
    }
}