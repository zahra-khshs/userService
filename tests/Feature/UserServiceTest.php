<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Redis;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationEmail;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_registration()
    {
         Mail::fake();

         $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

         $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'user', 'token']);
 $this->assertDatabaseHas('users', [
    'email' => 'test@example.com',
]);

 // $this->assertTrue(Redis::exists("user:{$userId}:session"));

Mail::assertSent(VerificationEmail::class, function ($mail) use ($response) {
    return $mail->hasTo('test@example.com');
});
    }
    public function test_user_login()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['token']);

         $this->assertTrue(Redis::exists("user:{$user->email}:token"));
    }

    public function test_get_user_profile()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->getJson('/api/profile', [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(200)
                 ->assertJson(['id' => $user->id]);
    }
}
