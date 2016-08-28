<?php

namespace Example\Template;

class FrontendTwigRenderer implements Renderer
{
    private $renderer;
    private $menuReader;

    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function render($template, $data = [])
    {
        return $this->renderer->render($template, $data);
    }
}
