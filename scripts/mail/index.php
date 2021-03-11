<?php
function envoiMail(String $to, String $subject, String $message)
{
    // Pour envoyer un mail HTML, l'en-tête Content-type doit être défini
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=utf-8';
    $headers[] = 'Reply-To: Matthieu Devilliers <devilliers.matthieu@gmail.com>';
    $headers[] = 'From: Matthieu Devilliers <devilliers.matthieu@gmail.com>';
    $headers[] = 'Bcc: Matthieu Devilliers <devilliers.matthieu@gmail.com>';
    $headers[] = 'X-Mailer: PHP/' . phpversion();

    // Envoi du mail
    return mail($to, $subject, $message, implode("\r\n", $headers));
}
