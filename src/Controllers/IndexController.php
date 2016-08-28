<?php

namespace Example\Controllers;

use Http\Request;
use Http\Response;
use Example\Core\Session;
use Example\Core\Text;
use Example\Core\Renderer;
use Example\Controllers\Controller;

class IndexController extends Controller
{
    private $request;
    private $response;
    private $renderer;

    public function __construct(
        Request $request,
        Response $response,
        Renderer $renderer
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->renderer = $renderer;
        parent::__construct();
    }

    public function show()
    {
        $html = $this->renderer->render('Homepage', $this->data);
        $this->response->setContent($html);
    }
}
