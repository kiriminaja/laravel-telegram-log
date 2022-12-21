<?php

namespace TelegramLog;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;
use Exception;

/*
 * Class TelegramHandler
 * @package App\TelegramLog
 */
class TelegramHandler extends AbstractProcessingHandler
{
    protected ?string $botToken, $appName, $appEnv;
    protected ?int $chatId;
    protected ?array $options;

    /**
     * @param int|string|Level $level
     * @param bool $bubble
     */
    public function __construct(int|string|Level $level = Level::Debug, bool $bubble = true)
    {
        parent::__construct($level, $bubble);

        // Define telegram request query
        $this->options = $this->getConfigValue("options");
        $this->botToken = $this->getConfigValue("token");
        $this->chatId = $this->getConfigValue("chat_id");

        // Define text message body
        $this->appName = config("app.name");
        $this->appEnv = config("app.env");
    }

    /**
     * @param LogRecord $record
     * @return void
     */
    protected function write(LogRecord $record): void
    {
        if(!$this->botToken || !$this->chatId) {
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

    private function sendMessage(string $text): void
    {
        $httpQuery = http_build_query(array_merge(
            [
                'text' => $text,
                'chat_id' => $this->chatId,
                'parse_mode' => 'html',
            ],
            $this->options
        ));

        file_get_contents('https://api.telegram.org/bot'.$this->botToken.'/sendMessage?' . $httpQuery);
    }

    /**
     * @param string $key
     * @param string|null $defaultConfigKey
     * @return string|array|null
     */
    private function getConfigValue(string $key, string|null $defaultConfigKey): string|array|null
    {
        if (isset($this->config[$key])) {
            return $this->config[$key];
        }

        return config($defaultConfigKey ?: "telegram-log.$key");
    }
}
