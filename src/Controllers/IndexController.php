<?php

namespace Example\Controllers;

class IndexController extends Controller
{
    public function index()
    {
        $html = $this->renderer->render('Index');
        $this->response->setContent($html);
    }
}
