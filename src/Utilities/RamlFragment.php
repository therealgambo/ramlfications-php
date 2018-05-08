<?php

namespace TheRealGambo\Ramlfications\Utilities;

class RamlFragment
{
    const DOCUMENTATIONITEM = 'DocumentationItem';
    const DATATYPE = 'DataType';
    const NAMEDEXAMPLE = 'NamedExample';
    const RESOURCETYPE = 'ResourceType';
    const _TRAIT = 'Trait';
    const ANNOTATIONTYPEDECLARATION = 'AnnontationTypeDeclaration';
    const LIBRARY = 'Library';
    const OVERLAY = 'Overlay';
    const EXTENSION = 'Extension';
    const SECURITYSCHEME = 'SecurityScheme';
    const _DEFAULT = 'Default';

    private $fragment = '';

    public function __construct($fragment)
    {
        $this->fragment = $fragment;
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }

    public static function create(string $name = ''): RamlFragment
    {
        if (strlen($name) === 0) {
            return new RamlFragment(self::_DEFAULT);
        }

        try {
            $name = 'self::' . strtoupper($name);
            if (defined($name)) {
                return new RamlFragment(constant($name));
            }
        } catch (\InvalidArgumentException $e) {
            throw new \Exception($name);
        }

        throw new \Exception('invalid fragment??');
    }
}
