<?php
/*
 * @Copyright (c) 2013 Leonel Quinteros
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are
 * met:
 *
 * * Redistributions of source code must retain the above copyright
 *   notice, this list of conditions and the following disclaimer.
 * * Redistributions in binary form must reproduce the above
 *   copyright notice, this list of conditions and the following disclaimer
 *   in the documentation and/or other materials provided with the
 *   distribution.
 * * Neither the name of the  nor the names of its
 *   contributors may be used to endorse or promote products derived from
 *   this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */

/**
 * PHP parser for TOML language: https://github.com/mojombo/toml
 *
 * @author Leonel Quinteros https://github.com/leonelquinteros
 *
 * @version 1.0
 *
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
        $pointer = & $result;

        // Pre-compile
        $toml = self::normalize($toml);

        // Split lines
        $aToml = explode("\n", $toml);

        foreach($aToml as $line)
        {
            $line = trim($line);

            // Skip commented and empty lines
            if(empty($line) || $line[0] == '#')
            {
                continue;
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

                $pointer[ trim($kv[0]) ] = self::parseValue( $kv[1] );
            }
        }

        return $result;
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
        // Cleanup EOL chars.
        $toml = str_replace(array("\r\n", "\n\r"), "\n", $toml);

        // Cleanup TABs
        $toml = str_replace("\t", " ", $toml);

        $normalized = '';
        $openBrackets = 0;
        $openString = false;

        for($i = 0; $i < strlen($toml); $i++)
        {
            $keep = true;

            if($toml[$i] == '[' && !$openString)
            {
                // Keygroup or array definition start outside a string
                $openBrackets++;
            }
            elseif($toml[$i] == ']' && !$openString)
            {
                // Keygroup or array definition end outside a string
                if($openBrackets > 0)
                {
                    $openBrackets--;
                }
                else
                {
                    throw new Exception("Unexpected ']' on line " . ($i + 1));
                }
            }
            elseif($openBrackets > 0 && $toml[$i] == "\n")
            {
                // EOLs inside array or Keygroup definition. We don't want them.
                $keep = false;
            }
            elseif($toml[$i] == '"' && $toml[$i - 1] != "\\")
            {
                // String handling, allow escaped quotes.
                $openString = !$openString;
            }
            elseif($toml[$i] == '#' && $openString == 0)
            {
                // Remove comments
                while($toml[$i] != "\n")
                {
                    $i++;
                }

                // Last char we know it's EOL.
                if($openBrackets)
                {
                    $keep = false;
                }
            }

            if($keep)
            {
                $normalized .= $toml[$i];
            }
        }

        // Something went wrong.
        if($openBrackets || $openString)
        {
            throw new Exception('Syntax error found on TOML document.');
        }

        return $normalized;
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
        $parsedVal = null;

        // Cleanup
        $val = trim($val);

        if(empty($val))
        {
            throw new Exception('Empty value not allowed');
        }

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
        // Single line array (normalized)
        elseif($val[0] == '[' && substr($val, -1) == ']')
        {
            $parsedVal = self::parseArray($val);
        }
        else
        {
            throw new Exception('Unknown value type: ' . $val);
        }

        return $parsedVal;
    }


    /**
     * Recursion function to parse all array values through self::parseValue()
     *
     * @param (array) $array
     *
     * @return (array) Parsed array.
     */
    private static function parseArray($val)
    {
        $result = array();
        $openBrackets = 0;
        $openString = false;
        $buffer = '';

        for($i = 0; $i < strlen($val); $i++)
        {
            if($val[$i] == '[' && !$openString)
            {
                $openBrackets++;

                if($openBrackets == 1)
                {
                    // Skip first and las brackets.
                    continue;
                }
            }
            elseif($val[$i] == ']' && !$openString)
            {
                $openBrackets--;

                if($openBrackets == 0)
                {
                    $result[] = self::parseValue( trim($buffer) );

                    // Skip first and las brackets. We're finish.
                    return $result;
                }
            }
            elseif($val[$i] == '"' && $val[$i - 1] != "\\")
            {
                $openString = !$openString;
            }

            if( $val[$i] == ',' && !$openString && $openBrackets == 1)
            {
                $result[] = self::parseValue( trim($buffer) );
                $buffer = '';
            }
            else
            {
                $buffer .= $val[$i];
            }
        }

        // If we're here, something went wrong.
        throw new Exception('Wrong array definition: ' . $val);
    }

}
