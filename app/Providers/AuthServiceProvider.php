<?php

namespace App\Providers;

use App\Models\ContactPeople;
use App\Models\Document;
use App\Models\Part;
use App\Models\Supplier;
use App\Models\User;
use App\Policies\ContactPeoplePolicy;
use App\Policies\DocumentPolicy;
use App\Policies\PartPolicy;
use App\Policies\SupplierPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     */
    protected $policies = [
        Document::class      => DocumentPolicy::class,
        ContactPeople::class => ContactPeoplePolicy::class,
        Supplier::class      => SupplierPolicy::class,
        User::class          => UserPolicy::class,
        Part::class          => PartPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
