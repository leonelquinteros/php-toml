<?php

require('../src/Toml.php');

$toml = "oneline = '''This string has a ' quote character.'''
firstnl = '''
This string has a ' quote character.'''
multiline = '''
This string
has ' a quote character
and more than
one newline
in it.'''
";

$result = Toml::parse($toml);

print_r($result);
echo json_encode($result);

echo "\n";
