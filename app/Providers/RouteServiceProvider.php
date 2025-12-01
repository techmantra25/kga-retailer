<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();
        $this->mapServicePartnerWebRoutes();
        $this->mapWebRoutes();
        $this->mapStaffRoutes();
        $this->mapCustomerRoutes();
        $this->mapDealerRoutes();
        $this->mapServicePartnerRoutes();
        $this->mapServiceCentreApiRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    protected function mapServicePartnerWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/servicepartnerweb.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }

    protected function mapStaffRoutes()
    {
        Route::prefix('staff')->name('staff.')
             ->namespace($this->namespace)
             ->group(base_path('routes/staff.php'));
    }

    protected function mapCustomerRoutes()
    {
        Route::prefix('customer')->name('customer.')
             ->namespace($this->namespace)
             ->group(base_path('routes/customer.php'));
    }

    protected function mapDealerRoutes()
    {
        Route::prefix('dealer')->name('dealer.')
             ->namespace($this->namespace)
             ->group(base_path('routes/dealer.php'));
    }

    protected function mapServicePartnerRoutes()
    {
        Route::prefix('servicepartner')->name('servicepartner.')
             ->namespace($this->namespace)
             ->group(base_path('routes/servicepartner.php'));
    }

    protected function mapServiceCentreApiRoutes()
    {
        Route::prefix('servicecentreapi')->name('servicecentreapi.')
             ->namespace($this->namespace)
             ->group(base_path('routes/servicecentreapi.php'));
    }
}
