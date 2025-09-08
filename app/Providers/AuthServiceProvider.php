<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use App\Models\Document;
use App\Models\ComplianceTemplate;
use App\Models\ComplianceReport;
use App\Policies\DocumentPolicy;
use App\Policies\ComplianceTemplatePolicy;
use App\Policies\ComplianceReportPolicy;
use App\Models\RiskReport;
use App\Policies\RiskReportPolicy;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Document::class => DocumentPolicy::class,
        \App\Models\DocumentAuditLog::class => \App\Policies\DocumentAuditLogPolicy::class,
        ComplianceTemplate::class => ComplianceTemplatePolicy::class,
        ComplianceReport::class => ComplianceReportPolicy::class,
        RiskReport::class => RiskReportPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {

        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });
    }
}
