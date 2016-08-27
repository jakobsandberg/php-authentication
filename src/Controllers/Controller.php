<?php

namespace Example\Controllers;

use Example\Core\Session;

class Controller
{
    public function __construct()
    {
        Session::start();
    }
}
