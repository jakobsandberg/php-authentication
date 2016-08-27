<?php

namespace Example\Core;

class Authentication
{
    public static function checkAuthentication()
    {
        Session::start();

        if (!Session::isLoggedIn()) {
            Session::end();
            Redirect::toPath("login");
        }
    }
}
