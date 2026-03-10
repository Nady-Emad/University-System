<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRoleRedirectTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_login_redirects_to_dashboard_by_role(): void
    {
        $this->post('/login', [
            'email' => 'admin@university.com',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        auth()->logout();

        $this->post('/login', [
            'email' => 'm.ali@university.com',
            'password' => 'password',
        ])->assertRedirect(route('doctor.dashboard'));

        auth()->logout();

        $this->post('/login', [
            'email' => 'ahmed.hassan@student.com',
            'password' => 'password',
        ])->assertRedirect(route('student.dashboard'));
    }

    public function test_role_middleware_blocks_unauthorized_sections(): void
    {
        $student = User::query()->where('email', 'ahmed.hassan@student.com')->firstOrFail();
        $doctor = User::query()->where('email', 'm.ali@university.com')->firstOrFail();
        $admin = User::query()->where('email', 'admin@university.com')->firstOrFail();

        $this->actingAs($student)->get(route('dashboard'))->assertForbidden();
        $this->actingAs($doctor)->get(route('student.dashboard'))->assertForbidden();
        $this->actingAs($admin)->get(route('doctor.dashboard'))->assertForbidden();
    }
}
