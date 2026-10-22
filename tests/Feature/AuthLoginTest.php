<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_login_with_login_field(): void
    {
        $response = $this->post('/login', [
            'login' => 'admin@demo.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticated();
    }

    public function test_staff_can_login_with_email_field(): void
    {
        $response = $this->post('/login', [
            'email' => 'sarah@demo.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_superadmin_can_login(): void
    {
        $response = $this->post('/login', [
            'login' => 'superadmin@demo.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticated();
    }

    public function test_admin_dashboard_renders_successfully(): void
    {
        $admin = User::where('username', 'admin')->first();
        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertOk();
    }

    public function test_staff_dashboard_renders_successfully(): void
    {
        $staff = User::where('username', 'sarah')->first();
        $response = $this->actingAs($staff)->get('/dashboard');
        $response->assertOk();
    }

    public function test_login_with_plain_username_admin(): void
    {
        $response = $this->post('/login', [
            'login' => 'admin',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticated();
    }

    public function test_login_with_plain_username_staff(): void
    {
        $response = $this->post('/login', [
            'login' => 'sarah',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }
}

