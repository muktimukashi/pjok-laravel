<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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
    public function test_dashboard_menu_is_limited_for_each_role(): void
    {
        $expectations = [
            'superadmin' => [
                'see' => ['data-page="userRole"', 'data-page="addUser"', 'data-page="userList"', 'data-page="audit"'],
                'dontSee' => ['data-page="students"', 'data-page="attendance"', 'data-page="recap"'],
            ],
            'admin' => [
                'see' => ['data-page="students"', 'data-page="teachers"', 'data-page="classes"', 'data-page="settings"'],
                'dontSee' => ['data-page="userRole"', 'data-page="attendance"', 'data-page="recap"'],
            ],
            'guru' => [
                'see' => ['data-page="attendance"', 'data-page="assessmentPlan"', 'data-page="recap"', 'data-page="criteriaRecap"'],
                'dontSee' => ['data-page="userRole"', 'data-page="students"', 'data-page="settings"'],
            ],
            'siswa' => [
                'see' => ['data-page="recap"'],
                'dontSee' => ['data-page="userRole"', 'data-page="students"', 'data-page="attendance"', 'data-page="criteriaRecap"'],
            ],
            'kepsek' => [
                'see' => [],
                'dontSee' => ['data-page="userRole"', 'data-page="students"', 'data-page="attendance"', 'data-page="recap"'],
            ],
        ];

        foreach ($expectations as $role => $expected) {
            $user = User::factory()->create(['role' => $role]);
            $response = $this->actingAs($user)->get('/dashboard');

            $response->assertOk();
            $response->assertSee('data-page="dashboard"', false);

            foreach ($expected['see'] as $menuMarker) {
                $response->assertSee($menuMarker, false);
            }

            foreach ($expected['dontSee'] as $menuMarker) {
                $response->assertDontSee($menuMarker, false);
            }
        }
    }

    public function test_non_admin_roles_cannot_sync_master_records(): void
    {
        foreach (['guru', 'siswa', 'kepsek'] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $response = $this->actingAs($user)->post('/records/sync', [
                'type' => 'classRecords',
                'records' => [
                    ['name' => 'Kelas Bocor'],
                ],
            ]);

            $response->assertForbidden();
        }
    }
    public function test_admin_can_import_students_from_csv(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $csv = UploadedFile::fake()->createWithContent('students.csv', "nis,nama,jenis_kelamin,email,status,kelas,tahun_ajaran,semester\n9001,Ani Sari,Perempuan,ani@example.test,Aktif,Kelas 1A,2025/2026,Ganjil\n9002,Budi Santoso,Laki-laki,budi@example.test,Aktif,Kelas 1A,2025/2026,Ganjil\n");

        $response = $this->actingAs($user)->post('/students/import-csv', [
            'csv' => $csv,
        ]);

        $response->assertOk()
            ->assertJsonPath('imported', 2)
            ->assertJsonPath('skipped', 0);

        $this->assertDatabaseHas('students', [
            'student_id' => '9001',
            'name' => 'Ani Sari',
            'class_name' => 'Kelas 1A',
        ]);
    }

    public function test_non_admin_roles_cannot_import_students_from_csv(): void
    {
        foreach (['guru', 'siswa', 'kepsek'] as $role) {
            $user = User::factory()->create(['role' => $role]);
            $csv = UploadedFile::fake()->createWithContent('students.csv', "nis,nama\n9001,Ani Sari\n");

            $response = $this->actingAs($user)->post('/students/import-csv', [
                'csv' => $csv,
            ]);

            $response->assertForbidden();
        }
    }
}

