<?php

namespace App\Policies;

use App\Models\PjokRecord;
use App\Models\User;

class PjokRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, PjokRecord $pjokRecord): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'superadmin'], true);
    }

    public function update(User $user, PjokRecord $pjokRecord): bool
    {
        return in_array($user->role, ['admin', 'superadmin'], true);
    }

    public function delete(User $user, PjokRecord $pjokRecord): bool
    {
        return in_array($user->role, ['admin', 'superadmin'], true);
    }
}
