<?php
namespace App\Service;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService {
    public function sendMail($to, $subject, $htmlContent) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'tokyherinjatorajoelison@gmail.com';
            $mail->Password = 'vlyy tllw ahsx ihmu'; //mot de passe d'application
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('tokyherinjatorajoelison@gmail.com', 'touky raj');
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlContent;

            $mail->send();
            return 'E-mail envoyé avec succés !';
        } catch (Exception $e) {
            return "L'e-mail n'a pas pu être envoyé. Erreur : {$mail->ErrorInfo}";
        }
    }
}

// 4f7bdff74056426eab3779aa7579dcc8 (api zerobounce)

