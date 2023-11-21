<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email {
    protected $email;
    protected $nombre;
    protected $token;

    public function __construct($email, $nombre, $token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion() {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'adb30325e1b6c0';
        $mail->Password = '1c75222f1ff936';

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress($this->email, 'Usuario Nuevo');
        $mail->Subject = 'Confirma tu Cuenta';

        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Has creado tu cuenta en UpTask, solo debes confirmarla en el siguiente link</p>";
        $contenido .= "<p>Presiona aqui: <a href='http://localhost:3000/confirmar?token=" . $this->token .  "'>Confirmar Cuenta</a></p>";
        $contenido .= "<p>Si tu no creaste esta cueta solo ignora el mensaje</p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;

        // Enviar el email
        $mail->send();
    }

    public function enviarInstrucciones() {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'adb30325e1b6c0';
        $mail->Password = '1c75222f1ff936';

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress($this->email, 'Crear Nueva Contraseña');
        $mail->Subject = 'Olvidaste tu password, Crea una nueva';

        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Parece que has olivado tu contraseña </p>";
        $contenido .= "<p>Presiona aqui, para crear una nueva: <a href='http://localhost:3000/restablecer?token=" . $this->token .  "'>Restablecer tu Contraseña</a></p>";
        $contenido .= "<p> Si tu no has solicitado esta recuperacion de cuenta, solo ignora el mensaje </p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;

        // Enviar el email
        $mail->send();
    }
}