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
     * @param Parsedown $parseDown
     */
    public function __construct(Parsedown $parseDown)
    {
        $this->parserService = $parseDown;
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
