<?php

namespace App\Providers;

use App\Models\PjokRecord;
use App\Policies\PjokRecordPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        PjokRecord::class => PjokRecordPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
