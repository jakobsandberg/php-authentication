<?php

namespace Example\Core;

class Session
{
    public static function start()
    {
        if (session_id() == "") {
            session_start();
        }
    }

    public static function add($key, $value)
    {
        $_SESSION[$key][] = $value;
    }

    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function get($key)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
    }

    public static function end()
    {
        session_destroy();
    }

    public static function isLoggedIn()
    {
        return(self::get("isLoggedIn") ?: false);
    }
}
