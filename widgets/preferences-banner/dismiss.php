<?php
session_start();

// Verify user is logged in
if (!isset($_SESSION['id_compte'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

// Set session variable to remember banner dismissal
$_SESSION['preferences_banner_dismissed'] = true;

// Return success
echo json_encode(['success' => true]);
?>
