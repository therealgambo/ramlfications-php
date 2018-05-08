<?php

namespace TheRealGambo\Ramlfications\Types;

use TheRealGambo\Ramlfications\Nodes\RootNode;
use TheRealGambo\Ramlfications\Nodes\TypeNode;
use TheRealGambo\Ramlfications\Utilities\TypeValidationError;

/**
 * JsonType class
 *
 * @see: https://github.com/raml-org/raml-spec/blob/master/versions/raml-10/raml-10.md#json-type
 */
class JsonType extends TypeNode
{
    /**
     * Json schema
     *
     * @var string
     **/
    private $json;

    public function __construct(array $raml, RootNode $rootNode, string $name)
    {
        parent::__construct($raml, $rootNode, $name);

        //?
        $this->json = $raml;
    }

    /**
     * Validate a JSON string against the schema
     * - Converts the string into a JSON object then uses the JsonSchema Validator to validate
     *
     * @param $string
     *
     * @return bool
     */
    public function validate($value)
    {
        $validator = new Validator();
        $jsonSchema = $this->json;

        $validator->check($value, $jsonSchema);

        if (!$validator->isValid()) {
            foreach ($validator->getErrors() as $error) {
                $this->errors[] = new TypeValidationError($error['property'], $error['constraint']);
            }
        }
    }
}
