<?php

namespace TheRealGambo\Ramlfications\Parameters;

use TheRealGambo\Ramlfications\Exceptions\InvalidDocumentationNodeException;

/**
 * Class Documentation
 *
 * User documentation for the API.
 *
 * @package TheRealGambo\Ramlfications\Parameters
 */
class Documentation
{
    /**
     * Title of documentation.
     *
     * @var string $title
     */
    private $title = '';

    /**
     * Content of documentation.
     *
     * @var string $content
     */
    private $content = '';

    private $annotations = [];


    public function __construct(array $raml)
    {
        if (isset($raml['title'])) {
            $this->title = $raml['title'];
        }

        if (isset($raml['content'])) {
            $this->content = $raml['content'];
        }

        $this->validate();
    }

    public function validate(): void
    {
        if (strlen($this->getTitle()->raw()) === 0) {
            throw new InvalidDocumentationNodeException('API documentation is missing a required \'title\' parameter.');
        }

        if (strlen($this->getContent()->raw()) === 0) {
            throw new InvalidDocumentationNodeException('API documentation is missing a required \'content\' parameter.');
        }
    }

    /**
     * Return the title of the documentation
     *
     * @return Content
     */
    public function getTitle(): Content
    {
        return new Content($this->title);
    }

    /**
     * Return the content of the documentation
     *
     * @return Content
     */
    public function getContent(): Content
    {
        return new Content($this->content);
    }
}
