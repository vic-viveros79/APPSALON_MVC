<?php 

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email {

    public $email;
    public $nombre;
    public $token;

    public function __construct($nombre, $email,  $token)
    {
      $this->nombre = $nombre;
      $this->email = $email;
      $this->token = $token;  
    }

    public function enviarConfirmacion() {
        //Crear objeto de email
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'f2d3b3343f190b';
        $mail->Password = '805837ee3d386c'; 

        $mail->setFrom('cuentas@appsalon.com');
        $mail->addAddress('cuentas@appsalon.com', 'Appsalon.com');
        $mail->Subject ='Confirma tu cuenta';

        //set html
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " .  $this->nombre . "</strong> Has creado tu cuenta en AppSalon, para confirmarla da click en e siguiente enlace</p>";
        $contenido .= "<p>Presiona aqui: <a href='http://localhost:3000/confirmar-cuenta?token=" . $this->token ."'>Confirmar cuenta</a></p>";
        $contenido .= "<p>Si no solicitaste esta cuenta, ignora el mensaje</p>";
        $contenido .= "</html>";
        $mail->Body = $contenido;

        //Enviar el mail
        $mail->send();
    }

    public function enviarInstrucciones() {
      //Crear objeto de email
      $mail = new PHPMailer();
      $mail->isSMTP();
      $mail->Host = 'smtp.mailtrap.io';
      $mail->SMTPAuth = true;
      $mail->Port = 2525;
      $mail->Username = 'f2d3b3343f190b';
      $mail->Password = '805837ee3d386c'; 

      $mail->setFrom('cuentas@appsalon.com');
      $mail->addAddress('cuentas@appsalon.com', 'Appsalon.com');
      $mail->Subject ='Reestablece tu password';

      //set html
      $mail->isHTML(TRUE);
      $mail->CharSet = 'UTF-8';

      $contenido = "<html>";
      $contenido .= "<p><strong>Hola " .  $this->nombre . "</strong> Has solicitado reestablecer tu password, para confirmarla da click en el siguiente enlace</p>";
      $contenido .= "<p>Presiona aqui: <a href='http://localhost:3000/recuperar?token=" . $this->token ."'>Reestablecer Password</a></p>";
      $contenido .= "<p>Si no solicitaste esta cuenta, ignora el mensaje</p>";
      $contenido .= "</html>";
      $mail->Body = $contenido;

      //Enviar el mail
      $mail->send();
    }
}

?>