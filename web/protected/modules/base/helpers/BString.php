<?php
/**
 * BString class file
 * @author Tudor Sandu <exromany@gmail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * class for processing strings
 * @package application.helpers
 */
class BString
{
    public static function camelCaseArray($string)
    {
        return preg_split('/([[:upper:]][[:lower:]]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
    }
    
    public static function camelToUnderscore($string)
    {
        return implode("_", self::camelCaseArray($string));
    }
}