<?php

namespace Tests\Feature\Auth\DTOs;

use App\Http\Requests\Auth\SignUpFormRequest;
use Domain\Auth\DTOs\NewUserDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewUserDTOTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_dto_from_request()
    {
        $dto = NewUserDTO::fromRequest(new SignUpFormRequest([
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'password' => 'secret123',
        ]));

        $this->assertInstanceOf(NewUserDTO::class, $dto);
        $this->assertSame('Alice', $dto->name);
        $this->assertSame('alice@example.com', $dto->email);
        $this->assertSame('secret123', $dto->password);
    }

    public function test_ignores_extra_fields_in_request()
    {
        $dto = NewUserDTO::fromRequest(new SignUpFormRequest([
            'name' => 'Bob',
            'email' => 'bob@example.com',
            'password' => 'supersecret',
            'role' => 'admin', // лишнее поле
        ]));

        $this->assertSame('Bob', $dto->name);
        $this->assertSame('bob@example.com', $dto->email);
        $this->assertSame('supersecret', $dto->password);
        $this->assertObjectNotHasProperty('role', $dto); // DTO не должен содержать поле role
    }

    public function test_is_immutable()
    {
        $dto = new NewUserDTO(
            name: 'Charlie',
            email: 'charlie@example.com',
            password: 'changeme',
        );

        $this->expectException(\Error::class);
        $dto->name = 'Hacker'; // Попытка изменить readonly-свойство
    }
}
