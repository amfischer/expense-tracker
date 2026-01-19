<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Formatter\IntlMoneyFormatter;
use Money\Parser\DecimalMoneyParser;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    public $singletons = [
        ISOCurrencies::class => ISOCurrencies::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // $this->app->singleton(ISOCurrencies::class, fn () => new ISOCurrencies());

        $this->app->singleton(IntlMoneyFormatter::class, function (Application $app) {
            $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);

            return new IntlMoneyFormatter($numberFormatter, $app->make(ISOCurrencies::class));
        });

        $this->app->singleton(DecimalMoneyFormatter::class, function (Application $app) {
            return new DecimalMoneyFormatter($app->make(ISOCurrencies::class));
        });

        $this->app->singleton(DecimalMoneyParser::class, function (Application $app) {
            return new DecimalMoneyParser($app->make(ISOCurrencies::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict();

        $this->app->resolving(IntlMoneyFormatter::class, function (IntlMoneyFormatter $c, Application $app) {
            // Called when container resolves objects of type "Transistor"...
            // ray('resolving ISOCurrencies', $c);
        });

        Gate::define('access-application', function (User $user) {
            return in_array($user->email, explode(',', config('custom.application_access')));
        });

        $this->bootRoute();
    }

    public function bootRoute(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

    }
}
