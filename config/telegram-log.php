<?php

return [
    'token'     => env('TELEGRAM_BOT_TOKEN'),
    'chat_id'   => env('TELEGRAM_CHAT_ID'),
    'template'  => env('TELEGRAM_LOGGER_TEMPLATE', 'laravel-telegram-log::minimal'),

    // Telegram sendMessage options: https://core.telegram.org/bots/api#sendmessage
    'options'   => json_decode(env('TELEGRAM_OPTIONS',"[]"))
];
