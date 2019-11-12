<?php

namespace App\Services;

use Parsedown;

/**
 * Class MarkdownToHTMLService
 * @package App\Services
 */
class MarkdownToHTMLService
{

    /**
     * @var Parsedown
     */
    private $parserService;


    /**
     * MarkdownToHTMLService constructor.
     */
    public function __construct()
    {
        $this->parserService = new Parsedown();
    }

    /**
     * @return Parsedown
     */
    public function getParserService(): Parsedown
    {
        return $this->parserService;
    }

    /**
     * @param string $markdown
     * @return string
     */
    public function convert(string $markdown): string
    {
        return $this->getParserService()->text($markdown);
    }
}
