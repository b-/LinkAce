<?php

namespace Tests\Controller\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_view(): void
    {
        $confirmView = $this->get('forgot-password');
        $confirmView->assertSee('forgot-password');
    }

    public function test_forgot_password_request(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'reset@linkace.org',
        ]);

        $response = $this->post('forgot-password', [
            'email' => 'reset@linkace.org',
        ]);

        $response->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_password_reset_view(): void
    {
        $user = User::factory()->create([
            'email' => 'reset@linkace.org',
        ]);

        $token = app('auth.password.broker')->createToken($user);

        $confirmView = $this->get('reset-password/' . $token);
        $confirmView->assertSee('reset-password');
    }

    public function test_password_reset_request(): void
    {
        $user = User::factory()->create([
            'email' => 'reset@linkace.org',
        ]);

        $token = app('auth.password.broker')->createToken($user);

        $confirmView = $this->post('reset-password/', [
            'token' => $token,
            'email' => 'reset@linkace.org',
            'password' => 'newPassword',
            'password_confirmation' => 'newPassword',
        ]);

        $confirmView->assertRedirect('login');

        $loginAttempt = Auth::attempt([
            'email' => 'reset@linkace.org',
            'password' => 'newPassword',
        ]);

        self::assertTrue($loginAttempt);
    }
}
