<?php

namespace Example\Controllers;

use Example\Core\Redirect;
use Example\Core\Session;
use Example\Models\RegistrationModel;

class RegistrationController extends Controller
{
    public function index()
    {
        Redirect::toHomeIfLoggedIn();
        $html = $this->renderer->render('Registration');
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
        $data = ["feedback" => Session::get("feedback")];
        $html = $this->renderer->render('Registration', $data);
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
            $data = ["feedback" => Session::get("feedback")];
            $html = $this->renderer->render('Registration', $data);
            $this->response->setContent($html);
        }
    }
}
