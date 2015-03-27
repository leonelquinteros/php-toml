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

// Format response values into test suite object
function walk(&$a) {
    foreach($a as $i => $v)
    {
        if(is_array($v))
        {
            walk($a[$i]);
        }
        else
        {
            // Get type
            $t = gettype($v);

            // Parse type name
            $t = str_replace(array('boolean', 'double'), array('bool', 'float'), $t);

            // Check date type
            if(Toml::isISODate($v))
            {
                $t = 'datetime';
            }

            // Fix double vs integer type
            if($t == 'float' && $v == intval($v))
            {
                $t = 'integer';
            }

            $a[$i] = array(
                'type' => $t,
                'value' => $v,
            );
        }
    }
}

walk($result);

echo json_encode($result);
