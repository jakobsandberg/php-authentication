<?php

namespace Example\Controllers;

use Http\Request;
use Http\Response;
use Example\Core\Renderer;
use Example\Core\Redirect;
use Example\Core\Session;
use Example\Core\Text;
use Example\Models\LoginModel;
use Example\Models\RegistrationModel;
use Example\Controllers\Controller;

class RegistrationController extends Controller
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
        $html = $this->renderer->render('Registration', $this->data);
        $this->response->setContent($html);
    }

    public function register()
    {
        $success = RegistrationModel::register();
        if ($success) {
            // different view for success
        } else {
            // different view for fail
        }
        $this->data['feedback'] = Session::get("feedback");
        $html = $this->renderer->render('Registration', $this->data);
        $this->response->setContent($html);
    }

    public function verify($params)
    {
        $userId = $params['userId'];
        $verCode = $params['verCode'];
        $success = RegistrationModel::verify($userId, $verCode);
        if ($success) {
            Redirect::home();
        } else {
            $this->data['feedback'] = Session::get("feedback");
            $html = $this->renderer->render('Registration', $this->data);
            $this->response->setContent($html);
        }
    }
}
