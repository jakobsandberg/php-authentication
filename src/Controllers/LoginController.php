<?php

namespace Example\Controllers;

use Example\Core\Redirect;
use Example\Core\Session;
use Example\Models\LoginModel;

class LoginController extends Controller
{
    public function index()
    {
        Redirect::toHomeIfLoggedIn();
        $html = $this->renderer->render('Login');
        $this->response->setContent($html);
    }

    public function login()
    {
        if (!LoginModel::login()) {
            $data = ["feedback" => Session::get("feedback")];
            $html = $this->renderer->render('Login', $data);
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
