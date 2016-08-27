<?php

namespace Example\Core;

use PHPMailer;

class Email
{
    private $error;

    public function send($to, $from, $fromName, $subject, $body)
    {
        $mail = new PHPMailer;

        $mail->IsSMTP();
        // 0 = off, 1 = commands, 2 = commands and data, perfect to see SMTP errors
        $mail->SMTPDebug = 0;
        // enable SMTP authentication
        $mail->SMTPAuth = Config::get('EMAIL_SMTP_AUTH');
        // encryption
        $mail->SMTPSecure = Config::get('EMAIL_SMTP_ENCRYPTION');
        // set SMTP provider's credentials
        $mail->Host = Config::get('EMAIL_SMTP_HOST');
        $mail->Username = Config::get('EMAIL_SMTP_USERNAME');
        $mail->Password = Config::get('EMAIL_SMTP_PASSWORD');
        $mail->Port = Config::get('EMAIL_SMTP_PORT');

        $mail->AddAddress($to);
        $mail->From = $from;
        $mail->FromName = $fromName;
        $mail->Subject = $subject;
        $mail->Body = $body;

        $success = $mail->Send();

        if ($success) {
            return true;
        } else {
            $this->error = $mail->ErrorInfo;
            return false;
        }
    }

    public function getError()
    {
        return $this->error;
    }
}
