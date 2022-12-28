<?php

namespace TelegramLog;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use Exception;

/*
 * Class TelegramHandler
 * @package App\TelegramLog
 */

class TelegramHandler extends AbstractProcessingHandler
{
    protected ?string $botToken, $appName, $appEnv;
    protected ?int                         $chatId;
    protected ?array                       $options;

    public function __construct(array $config)
    {
        $level = Logger::toMonologLevel($config['level']);

        parent::__construct($level, true);

        // Define telegram request query
        $this->options  = $this->getConfigValue("options");
        $this->botToken = $this->getConfigValue("token");
        $this->chatId   = $this->getConfigValue("chat_id");

        // Define text message body
        $this->appName = config("app.name");
        $this->appEnv  = config("app.env");
    }


    /**
     * @param array $record
     * @return void
     */
    protected function write(array $record): void
    {
        if (!$this->botToken || !$this->chatId) {
            throw new \InvalidArgumentException('Bot token or chat id is not defined for Telegram logger');
        }

        // trying to make request and send notification
        try {
            $textChunks = str_split($this->formatText($record), 4096);

            foreach ($textChunks as $textChunk) {
                $this->sendMessage($textChunk);
            }
        } catch (Exception $exception) {
            \Log::channel('single')->error($exception->getMessage());
        }
    }

    /**
     * @param string $text
     * @return void
     */
    private function sendMessage(string $text): void
    {
        $httpQuery = http_build_query(array_merge(
            [
                'text'       => $text,
                'chat_id'    => $this->chatId,
                'parse_mode' => 'html',
            ],
            $this->options
        ));

        $arrayContextOptions = [
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false
            ]
        ];

        file_get_contents(
            'https://api.telegram.org/bot' . $this->botToken . '/sendMessage?' . $httpQuery,
            false,
            stream_context_create($arrayContextOptions)
        );
    }

    /**
     * @param string $key
     * @param string|null $defaultConfigKey
     * @return string|array|null
     */
    private function getConfigValue(string $key, string $defaultConfigKey = null)
    {
        if (isset($this->config[$key])) {
            return $this->config[$key];
        }

        return config($defaultConfigKey ?: "telegram-log.$key");
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new LineFormatter("%message% %context% %extra%\n", null, false, true);
    }

    /**
     * @param array $record
     * @return string
     */
    private function formatText(array $record): string
    {
        if ($template = config('telegram-logger.template')) {
            return view($template, array_merge($record, [
                    'appName' => $this->appName,
                    'appEnv'  => $this->appEnv,
                ])
            );
        }

        return sprintf("<b>%s</b> (%s)\n%s", $this->appName, $record['level_name'], $record['formatted']);
    }

}
