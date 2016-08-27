<?php

namespace Example\Controllers;

use Http\Request;
use Http\Response;
use Example\Core\Session;
use Example\Template\FrontendRenderer;
use Example\Controllers\Controller;

class IndexController extends Controller
{
    private $request;
    private $response;
    private $renderer;

    public function __construct(
        Request $request,
        Response $response,
        FrontendRenderer $renderer
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->renderer = $renderer;
        parent::__construct();
    }

    public function show()
    {
        $data = [
            'isLoggedIn' => Session::isLoggedIn()
        ];
        $html = $this->renderer->render('Homepage', $data);
        $this->response->setContent($html);
    }
}
