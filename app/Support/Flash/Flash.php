<?php

namespace App\Support\Flash;

use Illuminate\Support\Facades\Session;

class Flash
{
    private const MESSAGE_KEY = 'shop_flash_message';
    private const MESSAGE_CLASS_KEY = 'shop_flash_class';

    public function get(): ?FlashMessage
    {
        $message = Session::get(self::MESSAGE_KEY);

        if (! $message) {
            return null;
        }

        return new FlashMessage(
            $message,
            Session::get(self::MESSAGE_CLASS_KEY, '')
        );
    }

    public function info(string $message)
    {
        $this->flash($message, 'info');
    }

    public function alert(string $message)
    {
        $this->flash($message, 'alert');
    }

    private function flash(string $message, string $name): void
    {
        Session::flash(self::MESSAGE_KEY, $message);
        Session::flash(self::MESSAGE_CLASS_KEY, config("flash.$name", ''));
    }
}
