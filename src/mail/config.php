<?php

// Email configuration
define('MAIL_FROM_EMAIL', 'noreply@localhost');
define('MAIL_FROM_NAME', 'Task Planner');

// Core email sending function using PHP's native mail()
function sendMail($to, $subject, $message) {
    // Set mail server configuration
    ini_set('SMTP', '127.0.0.1');
    ini_set('smtp_port', '25');
    ini_set('sendmail_from', MAIL_FROM_EMAIL);

    // Simple headers that should work with any SMTP server
    $headers = "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM_EMAIL . ">\r\n";
    $headers .= "Reply-To: " . MAIL_FROM_EMAIL . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    // Log the attempt
    error_log("Sending email to: $to");
    error_log("Subject: $subject");
    error_log("Headers: $headers");

    $result = mail($to, $subject, $message, $headers);
    error_log("Mail result: " . ($result ? "Success" : "Failed"));
    
    return $result;
}