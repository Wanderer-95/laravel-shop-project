<?php

namespace Services\Telegram;

use Illuminate\Support\Facades\Http;
use Services\Telegram\Exceptions\TelegramBotApiException;

class TelegramBotApi
{
    private const HOST = 'https://api.telegram.org/bot';

    public static function sendMessage(string $token, int $chatId, string $text): bool
    {
        try {
            $response = Http::asForm()->post(self::HOST.$token.'/sendMessage', [
                'chat_id' => $chatId,
                'text' => $text,
            ])->throw()->json();

            return $response['ok'] ?? false;
        } catch (\Throwable $e) {
            report(new TelegramBotApiException($e));

            return false;
        }
    }
}
