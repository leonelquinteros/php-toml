<?php
require("../src/Toml.php");

$toml = Toml::parseFile('example.toml');

if(count($argv) > 1 && $argv[1] == 'dump') {
    print_r($toml);
}