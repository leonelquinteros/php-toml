<?php

require('../src/Toml.php');

$toml = "answer = 42
neganswer = -42";

$result = Toml::parse($toml);

echo json_encode($result);
