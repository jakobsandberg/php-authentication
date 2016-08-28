<?php

namespace Example\Core;

use Twig_Environment;

class TwigRenderer implements Renderer
{
    private $engine;

    public function __construct(Twig_Environment $engine)
    {
        $this->engine = $engine;
    }

    public function render($template, $data = [])
    {
        $data = array_merge($data, [
            'GLOBAL' => Text::all(),
            'isLoggedIn' => Session::get("isLoggedIn")
        ]);
        return $this->engine->render("$template.html", $data);
    }
}
