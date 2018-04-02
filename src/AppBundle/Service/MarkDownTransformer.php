<?php

namespace AppBundle\Service;


use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Knp\Bundle\MarkdownBundle\Parser\MarkdownParser;

class MarkDownTransformer
{
    /** @var MarkdownParser */
    private $markdownParser;

    public function __construct(MarkdownParserInterface $markdownParser)
    {
        $this->markdownParser = $markdownParser;
    }

    public function parse(string $str) : string
    {
        return $this->markdownParser
            ->transformMarkdown($str);
    }
}