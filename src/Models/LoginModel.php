<?php

namespace Example\Models;

use Example\Core\Session;
use Example\Core\HttpMethod;
use Example\Core\Database;
use Example\Core\Text;

class LoginModel
{
    public static function login()
    {
        // Clear any previous feedback
        Session::set("feedback",[]);

        $email = HttpMethod::post("email", true);
        $password = HttpMethod::post("password", true);

        if (self::isDatabaseSearching(Session::get("falseEmailCounter"), Session::get("lastFailedLogin"))) {
            return false;
        }

        if (!self::validateAll($email, $password)) {
            return false;
        }

        $user = self::fetchUser($email);
        if (!$user) {
            return false;
        }

        if (self::isDatabaseSearching($user["failedLoginCounter"], $user["failedLoginTimestamp"])) {
            return false;
        }

        if (!self::isVerified($user["isVer"])) {
            return false;
        }

        if (!self::verifyPassword($email, $password, $user["password"])) {
            return false;
        }

        Session::set("isLoggedIn", true);
        Session::set("name", $user["name"]);
        Session::set("email", $user["email"]);

        return true;
    }

    public static function isDatabaseSearching($counter, $timestamp)
    {
        if (($counter > 2) AND ($timestamp > (time() - 30))) {
            Session::add("feedback", Text::get("LOGIN_FAILED_3_TIMES"));
            return true;
        }
    }

    public static function validateAll($email, $password)
    {
        if (self::validateEmail($email) AND self::validatePassword($password)) {
            return true;
        }

        return false;
    }

    public static function validateEmail($email)
    {
        if (empty($email)) {
            Session::add("feedback", Text::get("EMAIL_EMPTY"));
            return false;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::add("feedback", Text::get("EMAIL_PATTERN_FAIL"));
            return false;
        }

        return true;
    }

    public static function validatePassword($password)
    {
        if (empty($password)) {
            Session::add("feedback", Text::get("PASSWORD_EMPTY"));
            return false;
        }

        if (strlen($password) < 6) {
            Session::add("feedback", Text::get("PASSWORD_SHORT"));
            return false;
        }

        return true;
    }

    public static function fetchUser($email)
    {
        $connection = (new Database)->get();

        $sql = "SELECT email, password, name, isVer, failedLoginCounter, failedLoginTimestamp
                FROM users
                WHERE email=:email";
        $query = $connection->prepare($sql);
        $query->execute([
            ':email' => $email,
        ]);
        $row = $query->fetch();
        if (!$row) {
            self::updateSessionLog();
            Session::add("feedback", Text::get("LOGIN_INCORRECT"));
            return false;
        }

        self::updateSessionLog($reset = true);

        $user = [
            "email" => $row->email,
            "password" => $row->password,
            "name" => $row->name,
            "isVer" => $row->isVer,
            "failedLoginCounter" => $row->failedLoginCounter,
            "failedLoginTimestamp" => $row->failedLoginTimestamp
        ];

        return $user;
    }

    public static function updateSessionLog($reset = false)
    {
        if (!Session::get("falseEmailCounter")) {
            Session::set("falseEmailCounter", 0);
        }

        if ($reset) {
            $counter = 0;
            $time = "";
        } else {
            $counter = Session::get("falseEmailCounter") + 1;
            $time = time();
        }

        Session::set("falseEmailCounter", $counter);
        Session::set("lastFailedLogin", $time);
    }

    public static function isVerified($isVer)
    {
        if ($isVer != 1) {
            Session::add("feedback", Text::get("EMAIL_NOT_VERIFIED"));
            return false;
        }

        return true;
    }

    public static function verifyPassword($email, $password, $passwordDatabase)
    {
        if (!password_verify($password, $passwordDatabase)) {

            self::updateDatabaseLog($email);

            Session::add("feedback", Text::get("LOGIN_INCORRECT"));
            return false;
        }

        self::updateDatabaseLog($email, $reset = true);
        return true;
    }

    public static function updateDatabaseLog($email, $reset = false)
    {
        $connection = (new Database)->get();

        if ($reset) {
            $newTime = null;
            $sql = "UPDATE users
                    SET failedLoginCounter = 0,
                        failedLoginTimestamp = :failedLoginTimestamp
                    WHERE email=:email";
        } else {
            $newTime = time();
            $sql = "UPDATE users
                    SET failedLoginCounter = failedLoginCounter + 1,
                        failedLoginTimestamp = :failedLoginTimestamp
                    WHERE email=:email";
        }

        $query = $connection->prepare($sql);
        $query->execute([
            ':email' => $email,
            ':failedLoginTimestamp' => $newTime
        ]);
    }

    public static function logout()
    {
        Session::end();
    }
}
