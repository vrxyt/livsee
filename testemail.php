<?php

$email = 'fenrirthviti@gmail.com';
$authcode = random_int(100000, 999999);
$subject = 'DM Stream Account Verification';
$message = file_get_contents('inc/emailbefore.html');
$message .= "<a href=\"http://stream.rirnef.net/verify.php?email=$email&c=$authcode\" style=\"color: #fff!important;padding: 12px 24px;font-size: 29px;line-height: 1.3333333;border-radius: 3px;background-color: #df691a;border-color: transparent;display: inline-block;margin: auto;font-weight: normal;text-align: center;vertical-align: middle;touch-action: manipulation;cursor: pointer;background-image: none;border: 1px solid transparent;white-space: nowrap;-webkit-user-select: none;text-decoration: none;\">Verify Account</a>";
$message .= file_get_contents('inc/emailafter.html');
echo $message;
$headers = "From: DM Stream <noreply@rirnef.net>\r\n";
$headers .= "Reply-To: fenrir@rirnef.net\r\n";
$headers .= 'X-Mailer: PHP/' . phpversion();
$headers .= "CC: registration@rirnef.net\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
//mail($email, $subject, $message, $headers);
?>