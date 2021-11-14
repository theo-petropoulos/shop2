<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require ROOT . '/vendor/phpmailer/phpmailer/src/Exception.php';
require ROOT . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require ROOT . '/vendor/phpmailer/phpmailer/src/SMTP.php';

require ROOT . '/vendor/autoload.php';

$mail = new PHPMailer();

if(isset($table) && $table){
    switch($table){
        case 'register':
            $title = 'Votre inscription sur la boutique';
            $content = file_get_contents(ROOT . 'mailer/mails/register.html');
            $content = str_replace('$link', $link, $content);
            $content = str_replace('$firstname', $this->firstname, $content);
            break;
        case 'connect':
            $title = 'Nouvelle connexion à votre compte';
            $content = file_get_contents(ROOT . 'mailer/mails/connect.html');
            $content = str_replace('$link', $link, $content);
            $content = str_replace('$ip', $this->ip, $content);
            break;
        case 'delaccount':
            $title = 'Désolés de vous voir partir';
            $content = 'Votre compte a bien été supprimé.';
            break;
        case 'admin':
            $title = 'Connexion Administrateur';
            $content = file_get_contents(ROOT . 'mailer/mails/ADMconnect.html');
            $content = str_replace('$link', $link, $content);
            $this->mail = 'mpetropoulos.theo@gmail.com';
            break;
        default:
            $location = URL;
            header("Location: $location");
            break;
    }
}else die("Vous ne pouvez pas accéder à cette page.");

/**
 * Enable PoP & less-secure apps
 */

//Enable SMTP debugging. 
// $mail->SMTPDebug = 3;                               
//Set PHPMailer to use SMTP.
$mail->isSMTP();            
//Set SMTP host name                          
$mail->Host = "smtp.gmail.com";
//Set this to true if SMTP host requires authentication to send email
$mail->SMTPAuth = true;                          
//Provide username and password     
$mail->Username = "okko.network@gmail.com";                 
$mail->Password = "Test123!";                
//If SMTP requires TLS encryption then set it
//$mail->SMTPSecure = "tls";                           
//Set TCP port to connect to 
$mail->Port = 587;                                   

$mail->From = "okko.network@gmail.com";
$mail->FromName = "SHOP";

$mail->smtpConnect(
    array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
            "allow_self_signed" => true
        )
    )
);

$mail->AddAddress($this->mail);
$mail->WordWrap = 40; // set word wrap
$mail->Encoding = 'base64';
$mail->CharSet = "UTF-8";

$mail->IsHTML(true); // send as HTML

$mail->Subject = $title;
$mail->Body = $content;
$mail->AltBody = "Plain text"; //Text Body 

if(!$mail->Send())
    echo "Mailer Error: " . $mail->ErrorInfo;