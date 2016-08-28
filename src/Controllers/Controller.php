<?php

namespace Example\Controllers;

use Example\Core\Session;
use Example\Core\Text;

class Controller
{
    public function __construct()
    {
        Session::start();
    }
}
