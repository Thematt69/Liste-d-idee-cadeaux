<?php
session_start();

// Set session variable to remember banner dismissal
$_SESSION['preferences_banner_dismissed'] = true;

// Return success
echo json_encode(['success' => true]);
?>
