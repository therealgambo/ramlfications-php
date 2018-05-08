<?php

namespace TheRealGambo\Ramlfications\Parsers;

class RamlVersionParser
{
    const RAML_08 = '0.8';
    const RAML_10 = '1.0';

    private $version = RamlVersionParser::RAML_10;

    public function __construct($version)
    {
        $this->version = $version;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public static function parse(string $string): RamlVersionParser
    {
        if (in_array($string, [self::RAML_08, self::RAML_10])) {
            return new RamlVersionParser($string);
        }

        throw new \InvalidArgumentException('Invalid RAML version: ' . $string);
    }
}
