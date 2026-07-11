<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\PjokMasterData;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Super Admin', 'email' => 'superadmin@madep.test', 'role' => 'superadmin'],
            ['name' => 'Admin', 'email' => 'admin@madep.test', 'role' => 'admin'],
            ['name' => 'Guru PJOK', 'email' => 'guru@madep.test', 'role' => 'guru'],
            ['name' => 'Kepala Sekolah', 'email' => 'kepsek@madep.test', 'role' => 'kepsek'],
            ['name' => 'Siswa', 'email' => 'siswa@madep.test', 'role' => 'siswa'],
        ];

        foreach ($users as $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'role' => $user['role'],
                    'password' => Hash::make('password'),
                ],
            );
        }

        PjokMasterData::seedDefaults();
    }
}

