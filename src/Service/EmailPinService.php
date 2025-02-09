<?php

namespace App\Service;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailPinService
{
    private string $smtpHost = 'smtp.gmail.com';
    private string $smtpUsername = 'tokyherinjatorajoelison@gmail.com';
    private string $smtpPassword = 'vlyy tllw ahsx ihmu';
    private int $smtpPort = 587;

    public function generatePin(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function sendPinByEmail(string $email, string $pin): void
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $this->smtpHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->smtpUsername;
            $mail->Password   = $this->smtpPassword;
            $mail->SMTPSecure = 'tls';
            $mail->Port       = $this->smtpPort;

            $mail->setFrom($this->smtpUsername, 'touky raj');
            $mail->addAddress($email);
            $mail->Subject = 'Votre code de confirmation by Toky Rajoelison';
            $mail->Body    = "Votre code PIN est : $pin. Il est valable 90 secondes.";

            $mail->send();
        } catch (Exception $e) {
            throw new \Exception("Le message n'a pas pu être envoyé. Erreur : {$mail->ErrorInfo}");
        }
    }
}

// docker exec -i postgres_project pg_dump -U postgres -d identifiant > sauvegarde.sql

// pg_dump -U postgres -h localhost -d identifiant > sauvegarde.sql
