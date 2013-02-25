<?php

require("../src/toml.php");

$result = Toml::parseFile('example.toml');

echo "\n\nToml::parseFile('example.toml'): \n\n";
print_r($result);


$result = Toml::parseFile('example.toml');

echo "\n\nToml::parseFile('extended.toml'): \n\n";
print_r($result);
