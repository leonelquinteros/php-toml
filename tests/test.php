<?php

require("../src/toml.php");

$result = Toml::parseFile('example.toml');

echo "\n\nToml::parseFile() test \n";
print_r($result);
