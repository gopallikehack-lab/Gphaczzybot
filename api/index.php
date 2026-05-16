<?php
// Telegram Bot Webhook Handler for Vercel
header('Content-Type: application/json');

// Load all required files
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Get Telegram update data
$input = file_get_contents('php://input');
$update = json_decode($input, true);

if (!$update || empty($update)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'No update received']);
    exit;
}

// Process the update
processWebhookUpdate($update);

// Send success response
echo json_encode(['status' => 'ok']);
exit;
