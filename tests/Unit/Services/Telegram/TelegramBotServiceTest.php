<?php

namespace Tests\Unit\Services\Telegram;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Services\Telegram\Exceptions\TelegramBotApiException;
use Services\Telegram\TelegramBotApi;
use Tests\TestCase;

class TelegramBotServiceTest extends TestCase
{
    use RefreshDatabase;

    private string $token = 'dummy-token';

    private int $chatId = 123456;

    public function it_returns_true_when_message_sent_successfully()
    {
        Http::fake([
            "https://api.telegram.org/bot{$this->token}/sendMessage" => Http::response([
                'ok' => true,
                'result' => ['message_id' => 1],
            ], 200),
        ]);

        $result = TelegramBotApi::sendMessage($this->token, $this->chatId, 'hello');

        $this->assertTrue($result);

        Http::assertSent(function ($request) {
            return $request->url() === "https://api.telegram.org/bot{$this->token}/sendMessage"
                && $request['chat_id'] === $this->chatId
                && $request['text'] === 'hello';
        });
    }

    public function it_returns_false_if_api_returns_error()
    {
        Http::fake([
            "https://api.telegram.org/bot{$this->token}/sendMessage" => Http::response([
                'ok' => false,
                'description' => 'chat not found',
            ], 200),
        ]);

        $result = TelegramBotApi::sendMessage($this->token, $this->chatId, 'hello');

        $this->assertFalse($result);
    }

    public function it_returns_false_and_reports_exception_on_failure()
    {
        Http::fake([
            "https://api.telegram.org/bot{$this->token}/sendMessage" => Http::response(null, 500),
        ]);

        // Перехватим report, чтобы проверить что вызов был
        $this->expectsExceptionReport(TelegramBotApiException::class);

        $result = TelegramBotApi::sendMessage($this->token, $this->chatId, 'hello');

        $this->assertFalse($result);
    }

    protected function expectsExceptionReport(string $exceptionClass)
    {
        // Хук, чтобы тест не падал от report()
        $this->partialMock(ExceptionHandler::class, function ($mock) use ($exceptionClass) {
            $mock->shouldReceive('report')->once()->with(\Mockery::type($exceptionClass));
        });
    }
}
