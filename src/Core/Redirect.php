<?php

namespace Example\Core;

class Redirect
{
    public static function pageBeforeLogin($path)
    {
        header('Location: http://' . $_SERVER['HTTP_HOST'] . '/' . $path);
        exit();
    }

    public static function home()
    {
        header("Location: /");
        exit();
    }

    public static function toPath($path)
    {
        header("Location: /" . $path);
        exit();
    }

    public static function toHomeIfLoggedIn()
    {
        if (Session::isLoggedIn()) {
            self::home();
        }
    }
}
