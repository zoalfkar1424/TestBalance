<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //routes
        $this->registerPolicies();

        Passport::tokensCan(['user' => 'normal user', 'admin' => 'normal admin']);
        if (!$this->app->routesAreCached()) {
            //Passport::routes();
        }
    }
}
