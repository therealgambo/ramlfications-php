<?php

namespace TheRealGambo\Ramlfications\Types;

use Raml\Type;
use TheRealGambo\Ramlfications\Exceptions\Types\TypeInvalidValueForTypeException;
use TheRealGambo\Ramlfications\Nodes\RootNode;
use TheRealGambo\Ramlfications\Nodes\TypeNode;
use TheRealGambo\Ramlfications\Utilities\TypeValidationError;

/**
 * FileType class
 *
 * @see: https://github.com/raml-org/raml-spec/blob/master/versions/raml-10/raml-10.md#file-type
 */
class FileType extends TypeNode
{
    /**
     * A list of valid content-type strings for the file. The file type * / * MUST be a valid value.
     *
     * @var array
     **/
    private $fileTypes = [];

    /**
     * Specifies the minimum number of bytes for a parameter value. The value MUST be equal to or greater than 0.
     * Default: 0
     *
     * @var int
     **/
    private $minLength = 0;

    /**
     * Specifies the maximum number of bytes for a parameter value. The value MUST be equal to or greater than 0.
     * Default: 2147483647
     *
     * @var int
     **/
    private $maxLength = 2147483647;

    public function __construct(array $raml, RootNode $rootNode, string $name)
    {
        parent::__construct($raml, $rootNode, $name);

        if (isset($raml['fileTypes'])) {
            $this->setFileTypes($raml['fileTypes']);
        }

        if (isset($raml['minLength'])) {
            $this->setMinLength($raml['minLength']);
        }

        if (isset($raml['maxLength'])) {
            $this->setMaxLength($raml['maxLength']);
        }
    }

    /**
     * Get the value of File Types
     *
     * @return array
     */
    public function getFileTypes(): array
    {
        return $this->fileTypes;
    }

    /**
     * Set the value of File Types
     *
     * @param array $fileTypes
     *
     * @return self
     */
    public function setFileTypes(array $fileTypes): self
    {
        $this->fileTypes = $fileTypes;
        return $this;
    }

    /**
     * Get the value of Min Length
     *
     * @return int
     */
    public function getMinLength(): int
    {
        return $this->minLength;
    }

    /**
     * Set the value of Min Length
     *
     * @param int $minLength
     *
     * @return self
     */
    public function setMinLength(int $minLength): self
    {
        $this->minLength = $minLength;
        return $this;
    }

    /**
     * Get the value of Max Length
     *
     * @return int
     */
    public function getMaxLength(): int
    {
        return $this->maxLength;
    }

    /**
     * Set the value of Max Length
     *
     * @param int $maxLength
     *
     * @return self
     */
    public function setMaxLength(int $maxLength): self
    {
        $this->maxLength = $maxLength;
        return $this;
    }

    public function validate($value)
    {
        parent::validate($value);

        if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $value)) {
            throw new TypeInvalidValueForTypeException($this->getName(), 'file', $value);
        }
    }
}
