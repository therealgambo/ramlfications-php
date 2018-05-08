<?php

namespace TheRealGambo\Ramlfications\Parsers;

use TheRealGambo\Ramlfications\Exceptions\RamlHeaderException;
use TheRealGambo\Ramlfications\Utilities\RamlFragment;

class RamlHeaderParser
{
    const RAML_HEADER_PREFIX = '#%RAML';

    private $version;
    private $fragment = '';

    public function __construct(string $version, string $fragment = '')
    {
        $this->version  = $version;
        $this->fragment = $fragment;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }

    public function getRule()
    {
        if ($this->getVersion() === RamlVersionParser::RAML_08) {
            throw new \RuntimeException('not implemented yet');
        }

        return self::getFragmentRule($this->fragment);
    }

    public static function getFragmentRule($fragment)
    {
        switch($fragment) {
            case RamlFragment::DOCUMENTATIONITEM:

                break;
            case RamlFragment::DATATYPE:

                break;
            case RamlFragment::NAMEDEXAMPLE:

                break;
            case RamlFragment::RESOURCETYPE:

                break;
            case RamlFragment::TRAIT:

                break;
            case RamlFragment::ANNOTATIONTYPEDECLARATION:

                break;
            case RamlFragment::LIBRARY:

                break;
            case RamlFragment::OVERLAY:
            case RamlFragment::EXTENSION:

                break;
            case RamlFragment::SECURITYSCHEME:

                break;
            default:
                return null;
                break;
        }
    }

    public function __toString(): string
    {
        return self::RAML_HEADER_PREFIX . ' ' . $this->version . ' ' . $this->getFragment();
    }

    public static function parse(string $string)
    {
        list($prefix, $version, $scheme) = array_pad(explode(' ', $string), 3, '');

        // Check that the prefix matches
        if ($prefix !== self::RAML_HEADER_PREFIX) {
            throw new RamlHeaderException('Invalid RAML header: ' . $string);
        }

        // Parse and validate the version
        $version = RamlVersionParser::parse($version);

        // Parse and validate fragment type
        if ($version->getVersion() === RamlVersionParser::RAML_10) {
            $fragment = RamlFragment::create($scheme);
            return new RamlHeaderParser(RamlVersionParser::RAML_10, $fragment->getFragment());
        }

        return new RamlHeaderParser(RamlVersionParser::RAML_08);
    }
}
