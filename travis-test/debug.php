<?php
require("../src/Toml.php");

$str = <<<STR
[[fruit]]
  name = "apple"

[fruit.physical]
  color = "red"
  shape = "round"

[[fruit]]
  name = "banana"

STR;


$toml = Toml::parse($str);

print_r($toml);