<?php

namespace TheRealGambo\Ramlfications\Utilities;

use ICanBoogie\Inflector;

class StringTransformer
{
    const LOWER_CASE            = 1;
    const LOWER_CAMEL_CASE      = 2;
    const LOWER_HYPHEN_CASE     = 4;
    const LOWER_UNDERSCORE_CASE = 8;
    const UPPER_CASE            = 16;
    const UPPER_CAMEL_CASE      = 32;
    const UPPER_HYPHEN_CASE     = 64;
    const UPPER_UNDERSCORE_CASE = 128;
    const PLURALIZE             = 256;
    const SINGULARIZE           = 512;

    private static $possibleTransformations = [
        self::LOWER_CASE,
        self::UPPER_CASE,
        self::LOWER_CAMEL_CASE,
        self::LOWER_HYPHEN_CASE,
        self::LOWER_UNDERSCORE_CASE,
        self::UPPER_CAMEL_CASE,
        self::UPPER_HYPHEN_CASE,
        self::UPPER_UNDERSCORE_CASE,
        self::PLURALIZE,
        self::SINGULARIZE
    ];

    /**
     * Applies given function on string
     *
     * @param string $string
     * @param int    $convertTo
     *
     * @throws \Exception
     *
     * @return string
     **/
    public static function convertString(string $string, int $convertTo): string
    {
        if (!in_array($convertTo, self::$possibleTransformations)) {
            throw new \Exception(
                'Invalid transformation parameter "' . $convertTo . '" given for ' . __METHOD__
            );
        }

        $inflector = Inflector::get();

        switch ($convertTo) {
            case self::LOWER_CASE:
                $string = strtolower($string);
                break;
            case self::LOWER_CAMEL_CASE:
                $string = $inflector->camelize($string, Inflector::DOWNCASE_FIRST_LETTER);
                break;
            case self::LOWER_UNDERSCORE_CASE:
                $string = $inflector->underscore(
                    $inflector->camelize($string, Inflector::DOWNCASE_FIRST_LETTER)
                );
                break;
            case self::LOWER_HYPHEN_CASE:
                $string = $inflector->hyphenate(
                    $inflector->camelize($string, Inflector::DOWNCASE_FIRST_LETTER)
                );
                break;
            case self::UPPER_CASE:
                $string = strtoupper($string);
                break;
            case self::UPPER_CAMEL_CASE:
                $string = $inflector->camelize($string, Inflector::UPCASE_FIRST_LETTER);
                break;
            case self::UPPER_UNDERSCORE_CASE:
                $string = strtoupper($inflector->underscore($string));
                break;
            case self::UPPER_HYPHEN_CASE:
                $string = strtoupper($inflector->hyphenate($string));
                break;
            case self::PLURALIZE:
                $string = $inflector->pluralize($string);
                break;
            case self::SINGULARIZE:
                $string = $inflector->singularize($string);
                break;
        }

        return $string;
    }
}
