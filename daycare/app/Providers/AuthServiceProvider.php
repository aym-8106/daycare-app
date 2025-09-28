<?php

namespace App\Providers;

use App\Models\AttendanceRecord;
use App\Models\Office;
use App\Policies\AttendanceRecordPolicy;
use App\Policies\OfficePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Office::class => OfficePolicy::class,
        AttendanceRecord::class => AttendanceRecordPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}