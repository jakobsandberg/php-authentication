<?php

namespace Example\Controllers;

use Example\Core\Session;
use Example\Core\Text;

class Controller
{
    protected $data;

    public function __construct()
    {
        Session::start();
        $this->data = [
            'GLOBAL' => Text::all(),
            'isLoggedIn' => Session::isLoggedIn()
        ];
    }
}
