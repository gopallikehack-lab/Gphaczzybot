<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';

// Telegram API request function
function tgRequest($method, $data = []) {
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/" . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $resp = curl_exec($ch);
    curl_close($ch);
    return json_decode($resp, true);
}

// Send message function
function sendMessage($chat_id, $text, $keyboard = null) {
    $data = ['chat_id' => $chat_id, 'text' => $text, 'parse_mode' => 'HTML'];
    if ($keyboard) {
        $data['reply_markup'] = json_encode($keyboard);
    }
    return tgRequest('sendMessage', $data);
}

// Send photo function
function sendPhoto($chat_id, $photo, $caption = '', $keyboard = null) {
    $data = ['chat_id' => $chat_id, 'photo' => $photo, 'caption' => $caption, 'parse_mode' => 'HTML'];
    if ($keyboard) {
        $data['reply_markup'] = json_encode($keyboard);
    }
    return tgRequest('sendPhoto', $data);
}

// Main keyboard
function getMainKeyboard() {
    return [
        'keyboard' => [
            ['📸 BACK CAMERA', '🤳 FRONT CAMERA'],
            ['🎤 AUDIO HACK', '📍 LIVE LOCATION'],
            ['📱 MOBILE NUMBER', '🔗 URL SHORTENER'],
            ['📊 STATS', '❓ HELP']
        ],
        'resize_keyboard' => true
    ];
}

// Process webhook updates
function processWebhookUpdate($update) {
    global $IMAGES;
    $db = getDB();
    
    // Check if it's a message
    if (isset($update['message'])) {
        $msg = $update['message'];
        $chat_id = $msg['chat']['id'];
        $user_id = $msg['from']['id'];
        $first_name = $msg['from']['first_name'] ?? '';
        $text = trim($msg['text'] ?? '');
        
        // Save user to database
        $stmt = $db->prepare("INSERT OR REPLACE INTO users (user_id, username, first_name, last_name, last_active) VALUES (:user_id, :username, :first_name, :last_name, CURRENT_TIMESTAMP)");
        $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
        $stmt->bindValue(':username', $msg['from']['username'] ?? '', SQLITE3_TEXT);
        $stmt->bindValue(':first_name', $first_name, SQLITE3_TEXT);
        $stmt->bindValue(':last_name', $msg['from']['last_name'] ?? '', SQLITE3_TEXT);
        $stmt->execute();
        
        // Handle /start command
        if ($text == '/start') {
            $welcome_msg = "🔥 Welcome to BLACK HATS BOT! 🔥\n\n";
            $welcome_msg .= "✅ Hello {$first_name}!\n";
            $welcome_msg .= "📌 Your ID: <code>{$user_id}</code>\n\n";
            $welcome_msg .= "Select a tool from the menu below 👇";
            
            sendPhoto($chat_id, $IMAGES['MAIN'], $welcome_msg, getMainKeyboard());
        } 
        // Handle other commands
        elseif ($text == '/help') {
            sendMessage($chat_id, "❓ Help:\n/start - Start bot\n/help - This message", getMainKeyboard());
        }
        elseif ($text == '/stats') {
            // Count users
            $result = $db->query("SELECT COUNT(*) as count FROM users");
            $row = $result->fetchArray(SQLITE3_ASSOC);
            sendMessage($chat_id, "📊 Stats:\nTotal Users: " . $row['count'], getMainKeyboard());
        }
        else {
            // Check if user clicked a tool button
            $tools = ['📸 BACK CAMERA', '🤳 FRONT CAMERA', '🎤 AUDIO HACK', '📍 LIVE LOCATION', '📱 MOBILE NUMBER', '🔗 URL SHORTENER'];
            if (in_array($text, $tools)) {
                sendMessage($chat_id, "⚠️ Tool: {$text}\n\nThis feature is being set up. Coming soon!", getMainKeyboard());
            } else {
                sendMessage($chat_id, "❌ Unknown command. Use /start to begin.", getMainKeyboard());
            }
        }
    }
}
?>
