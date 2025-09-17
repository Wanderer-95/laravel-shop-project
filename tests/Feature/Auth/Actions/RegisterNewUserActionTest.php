<?php

namespace Tests\Feature\Auth\Actions;

use App\Http\Requests\Auth\SignUpFormRequest;
use Domain\Auth\Contracts\RegisterNewUserContract;
use Domain\Auth\DTOs\NewUserDTO;
use Domain\Auth\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterNewUserActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_a_user_and_logs_them_in()
    {
        Event::fake();

        $dto = NewUserDTO::fromRequest(new SignUpFormRequest([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'secret123',
        ]));

        $this->assertDatabaseMissing('users', ['email' => $dto->email]);

        $action = app(RegisterNewUserContract::class);

        $action($dto);

        $this->assertDatabaseHas('users', [
            'email' => $dto->email,
            'name' => 'John Doe',
        ]);

        $user = User::where('email', $dto->email)->first();

        $this->assertTrue(Hash::check('secret123', $user->password), 'Password should be hashed correctly');

        Event::assertDispatched(Registered::class, function ($event) use ($user) {
            return $event->user->is($user);
        });

        $this->assertTrue(Auth::check(), 'User should be logged in');
        $this->assertEquals($user->id, Auth::id());
    }
}
