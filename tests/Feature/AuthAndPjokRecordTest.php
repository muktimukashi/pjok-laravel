<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthAndPjokRecordTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_pjok_records(): void
    {
        $response = $this->get('/records');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_store_pjok_record(): void
    {
        $user = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)->post('/records', [
            'type' => 'strength',
            'code' => 'ABC123',
            'name' => 'Push Up',
            'payload' => ['score' => 5],
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('pjok_records', [
            'type' => 'strength',
            'code' => 'ABC123',
            'name' => 'Push Up',
        ]);
    }

    public function test_login_with_valid_credentials_works(): void
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/');
        $this->assertTrue(Auth::check());
    }

    public function test_admin_can_sync_master_records(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)->post('/records/sync', [
            'type' => 'classRecords',
            'records' => [
                ['name' => 'Kelas 1A'],
                ['name' => 'Kelas 1B'],
            ],
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('classes', [
            'name' => 'Kelas 1A',
        ]);
        $this->assertDatabaseCount('classes', 2);
    }

    public function test_superadmin_can_create_user(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
        ]);

        $response = $this->actingAs($user)->post('/users', [
            'name' => 'New Teacher',
            'email' => 'new.teacher@example.com',
            'role' => 'guru',
            'status' => 'Aktif',
            'password' => 'password',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('users', [
            'email' => 'new.teacher@example.com',
            'role' => 'guru',
            'status' => 'Aktif',
        ]);
    }

    public function test_admin_cannot_manage_users(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)->post('/users', [
            'name' => 'Blocked User',
            'email' => 'blocked@example.com',
            'role' => 'siswa',
            'status' => 'Aktif',
            'password' => 'password',
        ]);

        $response->assertForbidden();
    }
}

