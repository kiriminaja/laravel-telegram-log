<?php

return [
    'token'     => env('TELEGRAM_BOT_TOKEN'),
    'chat_id'   => env('TELEGRAM_CHAT_ID'),
    'template'  => env('TELEGRAM_LOGGER_TEMPLATE', 'laravel-telegram-log::minimal'),

    // Telegram sendMessage options: https://core.telegram.org/bots/api#sendmessage
    'options'   => [
        // 'parse_mode' => 'html',
        // 'disable_web_page_preview' => true,
        // 'disable_notification' => false
    ]
];
