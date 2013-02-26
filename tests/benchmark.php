<?php
require("../src/toml.php");

echo "\nMemory usage at start: " . memory_get_usage();

$start = microtime(true);

$result = Toml::parseFile('extended.toml');

$one = microtime(true);

echo "\nTooks " . (($one - $start) * 1000) . "s to parse 1 file";
echo "\nMemory usage at this point: " . memory_get_usage();


$start = microtime(true);

for($i = 0; $i < 1000; $i++)
{
    $result = Toml::parseFile('extended.toml');
}

$thousand = microtime(true);

echo "\nTooks " . (($thousand - $start) * 1000) . "s to parse 1000 times the same file";
echo "\nMemory usage at this point: " . memory_get_usage();


$start = microtime(true);

for($i = 0; $i < 100000; $i++)
{
    $result = Toml::parseFile('extended.toml');
}

$thousand = microtime(true);

echo "\nTooks " . (($thousand - $start) * 1000) . "s to parse 100.000 times the same file";
echo "\nMemory usage at this point: " . memory_get_usage();
echo "\n";
