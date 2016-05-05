<?php
/**
 * @author Michał Motyczko <michal@motyczko.pl>
 */

require (__DIR__ . '/../vendor/autoload.php');

if ($argc !== 3) {
    echo 'Usage: php mprofi-status.php apikey id' . PHP_EOL;
    exit(1);
}

$client = new \mprofi\Client($argv[1]);

$status = $client->getStatus($argv[2]);

echo "Message status: {$status->getStatus()}" . PHP_EOL;