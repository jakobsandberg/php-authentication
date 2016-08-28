<?php

namespace Example\Controllers;

use Http\Request;
use Http\Response;
use Example\Core\Renderer;
use Example\Core\Session;

class Controller
{
    protected $request;
    protected $response;
    protected $renderer;

    public function __construct(
        Request $request,
        Response $response,
        Renderer $renderer
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->renderer = $renderer;
        Session::start();
    }
}
