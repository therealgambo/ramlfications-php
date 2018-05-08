<?php

namespace TheRealGambo\Ramlfications\Parameters;

/**
 * Class Content
 *
 * Returns document-able content from the RAML file (e.g. Documentation content,
 * description) in either raw or parsed form.
 *
 * @package TheRealGambo\Ramlfications\Parameters
 */
class Content
{
    /**
     * The raw/marked up content data.
     *
     * @var string $data
     */
    private $data = '';

    /**
     * Content constructor.
     *
     * @param string $data
     */
    public function __construct(string $data)
    {
        $this->data = $data;
    }

    /**
     * Return raw Markdown/plain text written in the RAML file
     *
     * @return string
     */
    public function raw(): string
    {
        return $this->data;
    }

    /**
     *  Returns parsed Markdown into HTML
     *
     * @return string
     */
    public function html(): string
    {
        $parser = new \Parsedown();
        return $parser->text($this->data);
    }

    public function __toString(): string
    {
        return $this->raw();
    }
}
