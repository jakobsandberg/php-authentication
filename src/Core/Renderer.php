<?php

namespace Example\Core;

interface Renderer
{
    public function render($template, $data = []);
}
