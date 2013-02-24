<?php

require("../src/toml.php");

$result = Toml::parseFile('example.toml');

echo "\n\nToml::parseFile('example.toml'): \n";
echo "--------------------------------\n";
print_r($result);
