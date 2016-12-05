<?php
require("../src/Toml.php");

$str = <<<STR
[tab1]
foo = {bar='Donald Duck'}

STR;


$toml = Toml::parse($str);

print_r($toml);