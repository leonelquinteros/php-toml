<?php
/**
 * PHP parser for TOML language: https://github.com/mojombo/toml
 *
 * @author Leonel Quinteros https://github.com/leonelquinteros
 *
 * @version 1.0
 */
class Toml
{
    /**
     * Reads string from specified file path and parses it as TOML.
     *
     * @param (string) File path
     *
     * @return (array) Toml::parse() result
     */
    public static function parseFile($path)
    {
        if(!is_file($path))
        {
            throw new Exception('Invalid file path');
        }

        $toml = file_get_contents($path);
        return self::parse($toml);
    }


    /**
     * Parses a TOML string to retrieve a hashed array of data.
     *
     * @param (string) $toml TOML formatted string
     *
     * @return (array) Parsed TOML file into array.
     */
    public static function parse($toml)
    {
        $result = array();

        // Cleanup EOL chars.
        $toml = str_replace(array("\r\n", "\n\r"), "\n", $toml);

        // Cleanup TABs
        $toml = str_replace("\t", " ", $toml);

        // Pre-compile
        $toml = self::normalize($toml);

        // Split lines
        $aToml = explode("\n", $toml);

        foreach($aToml as $line)
        {
            $line = trim($line);

            // Skip comments
            if(empty($line) || $line[0] == '#')
            {
                continue;
            }
            elseif(strpos($line, '#'))
            {
                $lineSplit = explode('#', $line, 2);
                $line = trim($lineSplit[0]);
            }

            // Keygroup
            if($line[0] == '[' && substr($line, -1) == ']')
            {
                // Set pointer at first level.
                $pointer = & $result;

                $keygroup = substr($line, 1, -1);
                $aKeygroup = explode('.', $keygroup);

                foreach($aKeygroup as $keygroup)
                {
                    if( !isset($pointer[$keygroup]) )
                    {
                        $pointer[$keygroup] = array();
                    }

                    // Move pointer forward
                    $pointer = & $pointer[$keygroup];
                }
            }
            // Key = Values
            elseif(strpos($line, '='))
            {
                $kv = explode('=', $line, 2);

                // TODO: Implement multiline array sintax
                $pointer[ trim($kv[0]) ] = self::parseValue( $kv[1] );
            }
        }

        return $result;
    }


    /**
     * Parses TOML value and returns it to be assigned on the hashed array
     *
     * @param (string) $val
     *
     * @return (mixed) Parsed value.
     */
    private static function parseValue($val)
    {
        $parsedVal = 'Unknown';

        // Cleanup
        $val = trim($val);

        // Boolean
        if($val == 'true' || $val == 'false')
        {
            $parsedVal = (bool) $val;
        }
        // String
        elseif($val[0] == '"' && substr($val, -1) == '"')
        {
            $parsedVal = str_replace(array('\0', '\t', '\n', '\r', '\"', '\\') , array("\0", "\t", "\n", "\r", '"', "\\"), substr($val, 1, -1));
        }
        // Numbers
        elseif(is_numeric($val))
        {
            if(is_int($val))
            {
                $parsedVal = (int) $val;
            }
            else
            {
                $parsedVal = (float) $val;
            }
        }
        // Datetime. Parsed to UNIX time value.
        elseif(preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/', $val))
        {
            $parsedVal = strtotime($val);
        }
        // Single line array
        elseif($val[0] == '[' && substr($val, -1) == ']')
        {
            // TODO: Implement serious array support.

            $parsedVal = json_decode($val);
        }
        else
        {
            throw new Exception('Unknown value type: ' . $val);
        }

        return $parsedVal;
    }


    /**
     * Performs text modifications in order to normalize the TOML file for the parser.
     * Kind of pre-compiler.
     *
     * @param (string) $toml TOML string.
     *
     * @return (string) Normalized TOML string
     */
    private static function normalize($toml)
    {
        $normalized = '';
        $open = 0;

        for($i = 0; $i < strlen($toml); $i++)
        {
            $keep = true;

            if($toml[$i] == '[')
            {
                $open++;
            }
            elseif($toml[$i] == ']')
            {
                if($open > 0)
                {
                    $open--;
                }
                else
                {
                    throw new Exception("Unexpected ']' on line " . ($i + 1));
                }
            }
            elseif($open > 0 && $toml[$i] == "\n")
            {
                $keep = false;
            }

            if($keep)
            {
                $normalized .= $toml[$i];
            }
        }

        return $normalized;
    }
}
