<?php

// Clear session messages ONLY when requested via AJAX
// This should NOT run on normal page loads
if (isset($_SERVER['HTTP_X_CLEAR_MESSAGES']) && $_SERVER['HTTP_X_CLEAR_MESSAGES'] === 'true') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    unset($_SESSION['success']);
    unset($_SESSION['error']);
    unset($_SESSION['warning']);
    unset($_SESSION['info']);

    // Exit immediately for AJAX requests
    exit;
}

// For POST requests with clear_messages parameter (but NOT on initial page load)
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_messages'])) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    unset($_SESSION['success']);
    unset($_SESSION['error']);
    unset($_SESSION['warning']);
    unset($_SESSION['info']);
}
