<?php

namespace Example\Controllers;

use Http\Request;
use Http\Response;
use Example\Template\Renderer;
use Example\Core\Redirect;
use Example\Core\Session;
use Example\Models\LoginModel;
use Example\Controllers\Controller;

class LoginController extends Controller
{
    private $request;
    private $response;
    private $renderer;

    public function __construct(
        Request $request,
        Response $response,
        Renderer $renderer
    )
    {
        $this->request = $request;
        $this->response = $response;
        $this->renderer = $renderer;
        parent::__construct();
    }

    public function index()
    {
        Redirect::toHomeIfLoggedIn();
        $html = $this->renderer->render('Login', $this->data);
        $this->response->setContent($html);
    }

    public function login()
    {
        if (!LoginModel::login()) {
            $this->data['feedback'] = Session::get("feedback");
            $html = $this->renderer->render('Login', $this->data);
            $this->response->setContent($html);
        } else {
            Redirect::home();
        }
    }

    public function logout()
    {
        LoginModel::logout();
        Redirect::toPath("login");
    }
}
