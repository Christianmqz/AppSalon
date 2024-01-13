<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email {

    public $email;
    public $nombre;
    public $token;

    public function __construct($email, $nombre, $token) {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    private function configurarSMTP(PHPMailer $mail) {
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];
    }

    private function configurarDestinatario(PHPMailer $mail) {
        $mail->setFrom('christianm.10@hotmail.com');
        $mail->addAddress('christianm.10@hotmail.com', 'AppSalon.com');
    }

    private function enviarEmail(PHPMailer $mail, $subject, $content) {
        try {
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $subject;
            $mail->Body = $content;

            // Enviar el correo
            $mail->send();
        } catch (Exception $e) {
            // Handle the exception, you can log or display an error message
            echo "Error sending email: {$mail->ErrorInfo}";
        }
    }

    public function enviarConfirmacion() {
        $mail = new PHPMailer();

        // Configuración SMTP
        $this->configurarSMTP($mail);

        // Información del remitente y destinatario
        $this->configurarDestinatario($mail);

        // Contenido del Email
        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Has creado tu cuenta en App Salon, 
        solo debes confirmarla presionando el siguiente enlace...</p>";
        $contenido .= "<p>Presiona aquí: <a href='" .  $_ENV['PROJECT_URL']  . "/confirmar-cuenta?token=" . $this->token . 
        "'>Confirmar Cuenta</a> </p>";
        $contenido .= "<p>Si tú no solicitaste esta cuenta, puedes ignorar el mensaje.</p>";
        $contenido .= '</html>';

        $this->enviarEmail($mail, 'Confirma tu cuenta', $contenido);
    }

    public function enviarInstrucciones() {
        $mail = new PHPMailer();

        // Configuración SMTP
        $this->configurarSMTP($mail);

        // Información del remitente y destinatario
        $this->configurarDestinatario($mail);

        // Contenido del Email
        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Has solicitado restablecer 
        tu contraseña, sigue el siguiente enlace para hacerlo...</p>";
        $contenido .= "<p>Presiona aquí: <a href='" .  $_ENV['PROJECT_URL']  . "/confirmar-cuenta?token=" . $this->token . 
        "'>Restablecer Contraseña</a> </p>";
        $contenido .= "<p>Si tú no solicitaste esta cuenta, puedes ignorar el mensaje.</p>";
        $contenido .= '</html>';

        $this->enviarEmail($mail, 'Restablece tu Contraseña', $contenido);
    }
}
