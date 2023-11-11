#!php
<?php

use Dannyvdsluijs\AdventOfCode2023;
use Dannyvdsluijs\AdventOfCode2023\DayPreparation;

require_once 'vendor/autoload.php';

ini_set('memory_limit','2048M');

if ($argc < 2 || $argc > 3 || !is_numeric($argv[1])) {
    print("Usage ./run.php <day> [part]\r\nPossible values for part are: 'input', 1 or 2");
    exit(255);
}

if (($argv[2] ?? '') === 'input') {
    $preparation = new DayPreparation(2023, (int) $argv[1]);
    $preparation();
    exit(0);
}

$className = sprintf("\Dannyvdsluijs\AdventOfCode2023\Day%02d", $argv[1]);
$object = new $className();
$part = (int) ($argv[2] ?? 1);

$answer = match($part) {
    1 => $object->partOne(),
    2 => $object->partTwo(),
};


printf("The correct answer for day %d part %d is: %s\r\n", $argv[1], $part,  $answer);