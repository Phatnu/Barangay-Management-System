<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require '../vendor/autoload.php'; // ✅ Correct path
// require './settings.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

//Server settings
$mail->isSMTP();                   //Enable verbose debug output                                          //Send using SMTP
$mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
$mail->Username   = 'cuetopatrick91@gmail.com';                     //SMTP username
$mail->Password   = 'ykvu qzzl lnyd atbb';                               //SMTP password
$mail->SMTPSecure = 'tls';
$mail->Port = 587; 

