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
                $pointer = & $result;

                $keygroup = substr($line, 1, -1);
                $aKeygroup = explode('.', $keygroup);

                foreach($aKeygroup as $keygroup)
                {
                    if( !isset($pointer[$keygroup]) )
                    {
                        $pointer[$keygroup] = array();
                    }

                    $pointer = & $pointer[$keygroup];
                }
            }
            // Key = Values
            elseif(strpos($line, '='))
            {
                $kv = explode('=', $line, 2);

                $pointer[ trim($kv[0]) ] = self::parseValue( trim($kv[1]) );
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
        // TODO: Implement this.
        return $val;
    }
}
