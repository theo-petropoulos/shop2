<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require ROOT . '/vendor/phpmailer/phpmailer/src/Exception.php';
require ROOT . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require ROOT . '/vendor/phpmailer/phpmailer/src/SMTP.php';

require ROOT . '/vendor/autoload.php';

$mail = new PHPMailer();

if(isset($message) && $message){
    switch($message){
        case 'register':
            $title = 'Votre inscription sur la boutique';
            $content = file_get_contents(ROOT . 'mailer/mails/register.html');
            $content = str_replace('$link', $link, $content);
            $content = str_replace('$login', $firstname, $content);
            break;
        case 'delaccount':
            $title = 'Désolés de vous voir partir';
            $content = 'Votre compte a bien été supprimé.';
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
$mail->FromName = "OKKO";

$mail->smtpConnect(
    array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
            "allow_self_signed" => true
        )
    )
);

$mail->AddAddress($mail_address);
$mail->WordWrap = 40; // set word wrap
$mail->Encoding = 'base64';
$mail->CharSet = "UTF-8";

$mail->IsHTML(true); // send as HTML

$mail->Subject = $title;
$mail->Body = $content;
$mail->AltBody = "Plain text"; //Text Body 

if(!$mail->Send())
{
echo "Mailer Error: " . $mail->ErrorInfo;
}
else{

}