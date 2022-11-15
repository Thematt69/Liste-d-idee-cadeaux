<?php
function envoiMail(String $to, String $subject, String $message)
{
    // Pour envoyer un mail HTML, l'en-tête Content-type doit être défini
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=utf-8';
    $headers[] = 'Reply-To: Matthieu Devilliers <webmaster@matthieudevilliers.fr>';
    $headers[] = 'From: Service Client <webmaster@matthieudevilliers.fr>';
    // FIXME - Enlever avant commercialisation
    $headers[] = 'Bcc: Matthieu Devilliers <webmaster@matthieudevilliers.fr>';
    $headers[] = 'X-Mailer: PHP/' . phpversion();

    // Envoi du mail
    return mail($to, $subject, $message, implode("\r\n", $headers));
}
