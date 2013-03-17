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

        // Run, char by char.
        $normalized     = '';
        $openString     = false;
        $openBrackets   = 0;
        $openKeygroup   = false;
        $lineBuffer     = '';

        $strLen = strlen($toml);
        for($i = 0; $i < $strLen; $i++)
        {
            $keep = true;

            if($toml[$i] == '[' && !$openString)
            {
                // Keygroup or array definition start outside a string
                $openBrackets++;

                // Keygroup
                if($openBrackets == 1 && trim($lineBuffer) == '')
                {
                    $openKeygroup = true;
                }
            }
            elseif($toml[$i] == ']' && !$openString)
            {
                // Keygroup or array definition end outside a string
                if($openBrackets > 0)
                {
                    $openBrackets--;

                    if($openKeygroup)
                    {
                        $openKeygroup = false;
                    }
                }
                else
                {
                    throw new Exception("Unexpected ']' on: " . $lineBuffer);
                }
            }
            elseif($openBrackets > 0 && $toml[$i] == "\n")
            {
                // Multi-line keygroup definition is not alowed.
                if($openKeygroup)
                {
                    throw new Exception('Multi-line keygroup definition is not allowed on: ' . $lineBuffer);
                }

                // EOLs inside array definition. We don't want them.
                $keep = false;
            }
            elseif($openString && $toml[$i] == "\n")
            {
                // EOLs inside string should throw error.
                throw new Exception("Multi-line strings are not allowed on: " . $lineBuffer);
            }
            elseif($toml[$i] == '"' && $toml[$i - 1] != "\\")
            {
                // String handling, allow escaped quotes.
                $openString = !$openString;
            }
            elseif($toml[$i] == "\\" && !in_array($toml[$i+1], array('0', 't', 'n', 'r', '"', "\\")))
            {
                // Reserved special characters should produce error
                throw new Exception('Reserved special characters inside strings are not allowed: ' . $toml[$i] . $toml[$i+1]);
            }
            elseif($toml[$i] == '#' && !$openString && !$openKeygroup)
            {
                // Remove comments only at the end of the line. Doesn't catch comments inside array definition.
                while(isset($toml[$i]) && $toml[$i] != "\n")
                {
                    $i++;
                }

                // Last char we know it's EOL.
                $keep = ($openBrackets == 0);
            }

            // Raw Lines
            $lineBuffer .= $toml[$i];
            if($toml[$i] == "\n")
            {
                $lineBuffer = '';
            }

            if($keep)
            {
                $normalized .= $toml[$i];
            }
        }

        // Something went wrong.
        if($openBrackets || $openString || $openKeygroup)
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
        elseif(self::isISODate($val))
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

        $strLen = strlen($val);
        for($i = 0; $i < $strLen; $i++)
        {
            if($val[$i] == '[' && !$openString)
            {
                $openBrackets++;

                if($openBrackets == 1)
                {
                    // Skip first and last brackets.
                    continue;
                }
            }
            elseif($val[$i] == ']' && !$openString)
            {
                $openBrackets--;

                if($openBrackets == 0)
                {
                    // Allow terminating commas before the closing bracket
                    if(trim($buffer) != '')
                    {
                        $result[] = self::parseValue( trim($buffer) );
                    }

                    if (!self::checkDataType($result))
                    {
                        throw new Exception('Data types cannot be mixed in an array: ' . $buffer);
                    }
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

                if (!self::checkDataType($result))
                {
                    throw new Exception('Data types cannot be mixed in an array: ' . $buffer);
                }
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

    /**
     * Function that checks the data type of the first and last elements of an array,
     * and returns false if they don't match
     *
     * @param  (array) $array
     *
     * @return boolean
     */
    private static function checkDataType($array)
    {

        if(count($array) <= 1)
        {
            return true;
        }

        $last = count($array) - 1;

        $type = self::getCustomDataType($array[$last]);

        if ($type != self::getCustomDataType($array[0]))
        {
            return false;
        }
        else
        {
            return true;
        }

    }

    /**
     * Returns the data type of a variable
     *
     * @param  (mixed) $val
     * @return (string) Data type of value
     */
    private static function getCustomDataType($val)
    {
        $val = (!is_array($val)) ? trim($val) : $val;

        if (!is_array($val) && self::isISODate($val))
        {
            $type = "date";
        }
        else
        {
            $type = gettype($val);
        }

        return $type;
    }

    /**
     * Return whether the given value is a valid ISODate
     *
     * @param  (string)  $val
     * @return boolean
     */
    private static function isISODate($val)
    {
        return preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/', $val);
    }

}
