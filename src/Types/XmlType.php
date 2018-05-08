<?php

namespace TheRealGambo\Ramlfications\Types;

use DOMDocument;
use TheRealGambo\Ramlfications\Nodes\RootNode;
use TheRealGambo\Ramlfications\Nodes\TypeNode;
use TheRealGambo\Ramlfications\Utilities\TypeValidationError;

/**
 * XmlType class
 *
 * @see: https://github.com/raml-org/raml-spec/blob/master/versions/raml-10/raml-10.md#xml-type
 */
class XmlType extends TypeNode
{
    /**
     * XML schema
     *
     * @var string
     **/
    private $xml;

    public function __construct(array $raml, RootNode $rootNode, string $name)
    {
        parent::__construct($raml, $rootNode, $name);

        $this->xml = $raml;
    }

    /**
     * Validate an XML string against the schema
     *
     * @param $string
     */
    public function validate($value)
    {
        if (!$value instanceof DOMDocument) {
            $this->errors[] = TypeValidationError::xmlValidationFailed('Expected value of type DOMDocument');
            return;
        }

        $originalErrorLevel = libxml_use_internal_errors(true);
        $value->schemaValidateSource($this->xml);
        $errors = libxml_get_errors();
        libxml_clear_errors();
        if ($errors) {
            foreach ($errors as $error) {
                $this->errors[] = TypeValidationError::xmlValidationFailed($error->message);
            }

            return;
        }

        libxml_use_internal_errors($originalErrorLevel);
    }
}
