<?php

namespace TelegramLog;

use Monolog\Logger;

/*
 * Class TelegramLogger
 * @package App\TelegramLog
 */
class TelegramLogger
{
    /**
     * Create a custom Monolog instance.
     *
     * @param  array  $config
     * @return Logger
     */
    public function __invoke(array $config): Logger
    {
        return new Logger(
            config('app.name'),
            [
                new TelegramHandler($config),
            ]
        );
    }
}
