<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    private const ROLES = ['superadmin', 'admin', 'guru', 'kepsek', 'siswa'];
    private const STATUSES = ['Aktif', 'Nonaktif'];

    public function index(Request $request)
    {
        $this->authorizeSuperadmin($request);

        return response()->json(self::userRecords());
    }

    public function store(Request $request)
    {
        $this->authorizeSuperadmin($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(self::ROLES)],
            'status' => ['required', Rule::in(self::STATUSES)],
            'password' => ['required', 'string', 'min:6'],
        ]);

        User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'status' => $validated['status'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json(self::userRecords(), 201);
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeSuperadmin($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(self::ROLES)],
            'status' => ['required', Rule::in(self::STATUSES)],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'status' => $validated['status'],
        ];

        if (! empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $user->update($payload);

        return response()->json(self::userRecords());
    }

    public function destroy(Request $request, User $user)
    {
        $this->authorizeSuperadmin($request);

        abort_if($request->user()->is($user), 422, 'User aktif tidak bisa menghapus akun sendiri.');

        $user->delete();

        return response()->json(self::userRecords());
    }

    public static function userRecords(): array
    {
        return User::query()->orderBy('name')->get()->map(fn (User $user) => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->status ?? 'Aktif',
        ])->values()->all();
    }

    private function authorizeSuperadmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'superadmin', 403);
    }
}
