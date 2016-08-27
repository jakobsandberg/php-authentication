<?php

namespace Example\Models;

use Example\Core\Session;
use Example\Core\HttpMethod;
use Example\Core\Database;
use Example\Core\Text;
use Example\Core\Email;
use Example\Core\Config;

class RegistrationModel
{
    public static function register()
    {
        $name = HttpMethod::post("name", true);
        $email = HttpMethod::post("email", true);
        $emailRepeat = HttpMethod::post("emailRepeat", true);
        $password = HttpMethod::post("password", true);
        $passwordRepeat = HttpMethod::post("passwordRepeat", true);

        // Clear any previous feedback
        Session::set("feedback",[]);

        if (!self::validateAll($name, $email, $emailRepeat, $password, $passwordRepeat)) {
            return false;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        if (self::emailExists($email)) {
            //return false;
        }

        $verCode = sha1(uniqid(mt_rand(), true));

        if (!self::insertIntoDatabase($name, $email, $passwordHash, $verCode)) {
            return false;
        }

        $userId = self::getUserId($email);
        if (!$userId) {
            return false;
        }

        if(!self::verificationEmail($userId, $email, $verCode)) {
            self::deleteUser($userId);
            return false;
        }

        Session::add("feedback", Text::get("REGISTRATION_SUCCESS"));

        return true;
    }

    public static function validateAll($name, $email, $emailRepeat, $password, $passwordRepeat)
    {
        // validate all inputs
        if (self::validateName($name) AND self::validateEmail($email, $emailRepeat) AND self::validatePassword($password, $passwordRepeat)) {
            return true;
        }

        return false;
    }

    public static function validateName($name)
    {
        if (empty($name)) {
            Session::add("feedback", Text::get("NAME_EMPTY"));
            return false;
        }

        return true;
    }

    public static function validateEmail($email, $emailRepeat)
    {
        if ($email !== $emailRepeat) {
            Session::add("feedback", Text::get("EMAIL_MISMATCH"));
            return false;
        }

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

    public static function validatePassword($password, $passwordRepeat)
    {
        if ($password !== $passwordRepeat) {
            Session::add("feedback", Text::get("PASSWORD_MISMATCH"));
            return false;
        }

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

    public static function emailExists($email)
    {
        $connection = (new Database)->get();

        $sql = "SELECT email
                FROM users
                WHERE email=:email";
        $query = $connection->prepare($sql);
        $query->execute([
            ':email' => $email,
        ]);
        $row = $query->fetch();
        if ($row) {
            if ($email === $row->email) {
                Session::add("feedback", Text::get("EMAIL_EXISTS"));
                return true;
            }
        }
        return false;
    }

    public static function insertIntoDatabase($name, $email, $password, $verCode)
    {
        $connection = (new Database)->get();

        $sql = "INSERT INTO users (name, email, password, verCode)
                VALUES (:name, :email, :password, :verCode)";
        $query = $connection->prepare($sql);
        $query->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $password,
            ':verCode' => $verCode
        ]);
        if ($query->rowCount() == 1) {
            return true;
        }

        Session::add("feedback", Text::get("INSERT_FAILED"));
        return false;
    }

    public static function getUserId($email) {
        $connection = (new Database)->get();

        $sql = "SELECT userId
                FROM users
                WHERE email=:email";
        $query = $connection->prepare($sql);
        $query->execute([
            ':email' => $email,
        ]);
        $row = $query->fetch();
        if (!$row->userId) {
            Session::add("feedback", Text::get("UNKNOWN_ERROR"));
            return false;
        }
        return $row->userId;
    }

    public static function verificationEmail($userId, $email, $verCode)
    {
        $from = Config::get('EMAIL_SMTP_USERNAME');
        $fromName = Config::get('EMAIL_SMTP_NAME');
        //$body = "Your email was successfully registered! ".$verCode;
        $body = Text::get('EMAIL_CONTENT') . Config::get('URL') .
                Config::get('VERIFICATION_URL') . '/' . urlencode($userId) .
                '/' . urlencode($verCode);
        $subject = "Your registration was successful!";

        $mail = new Email;

        $send = $mail->send($email, $from, $fromName, $subject, $body);

        if (!$send) {
            Session::add("feedback", Text::get("EMAIL_FAILED").$mail->getError());
            return false;
        }

        return true;
    }

    public static function deleteUser($userId)
    {
        $connection = (new Database)->get();

        $sql = "DELETE FROM users
                WHERE userId=:userId";
        $query = $connection->prepare($sql);
        $query->execute([
            ':userId' => $userId,
        ]);
    }

    public static function verify($userId, $verCode)
    {
        $connection = (new Database)->get();

        $sql = "UPDATE users
                SET isVer=:isVer
                WHERE userId=:userId AND verCode=:verCode";
        $query = $connection->prepare($sql);
        $query->execute([
            ':userId' => $userId,
            ':verCode' => $verCode,
            ':isVer' => 1
        ]);
        if ($query->rowCount() == 1) {
            return true;
        }

        // Clear any previous feedback
        Session::set("feedback",[]);

        Session::add("feedback", Text::get("VERIFICATION_FAILED"));
        return false;
    }
}
