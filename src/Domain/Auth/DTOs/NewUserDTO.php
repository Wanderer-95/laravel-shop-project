<?php

namespace Domain\Auth\DTOs;

use Illuminate\Http\Request;
use Support\Traits\MakeableTrait;

class NewUserDTO
{
    use MakeableTrait;

    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return self::make(...$request->only(['name', 'email', 'password']));
    }
}
