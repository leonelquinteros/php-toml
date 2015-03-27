#!/usr/bin/php
<?php
require('../src/Toml.php');

try
{
    $result = Toml::parse(file_get_contents("php://stdin"));
}
catch(Exception $e)
{
    exit(1);
}

echo json_encode($result);
