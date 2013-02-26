<?php
require("../src/toml.php");

echo "\nMemory usage at start: " . memory_get_usage();

$start = microtime(true);

$result = Toml::parseFile('extended.toml');

$one = microtime(true);

echo "\nTooks " . ($one - $start) . "s to parse 1 file";
echo "\nMemory usage at this point: " . memory_get_usage();


$start = microtime(true);

for($i = 0; $i < 1000; $i++)
{
    $result = Toml::parseFile('extended.toml');
}

$thousand = microtime(true);

echo "\nTooks " . ($thousand - $start) . "s to parse 1000 times the same file";
echo "\nMemory usage at this point: " . memory_get_usage();


$start = microtime(true);

for($i = 0; $i < 10000; $i++)
{
    $result = Toml::parseFile('extended.toml');
}

$thousand = microtime(true);

echo "\nTooks " . ($thousand - $start) . "s to parse 10.000 times the same file";
echo "\nMemory usage at this point: " . memory_get_usage();
echo "\n";
