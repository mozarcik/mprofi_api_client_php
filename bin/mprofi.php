<?php
/**
 * @author Michał Motyczko <michal@motyczko.pl>
 */

require (__DIR__ . '/../vendor/autoload.php');

if ($argc !== 4) {
    echo 'Usage: php mprofi.php apikey recipient message' . PHP_EOL;
    exit(1);
}

$message = new \mprofi\Message($argv[2], $argv[3]);
$client = new \mprofi\Client($argv[1]);

print_r($client->send($message));