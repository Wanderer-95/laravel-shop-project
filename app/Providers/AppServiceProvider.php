<?php

namespace App\Providers;

use Carbon\CarbonInterval;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Generator::class, function ($app) {
            $faker = Factory::create();
            $faker->addProvider(new FakerImageProvider($faker));

            return $faker;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureReteLimiting();
        Model::shouldBeStrict(! app()->isProduction());

        if (app()->isProduction()) {

            DB::whenQueryingForLongerThan(CarbonInterval::seconds(5), function (Connection $connection) {
                Log::channel('stack')->info('whenQueryingForLongerThan: '.$connection->query()->toSql());
            });

            DB::listen(function (QueryExecuted $query) {
                if ($query->time > 100) {
                    Log::channel('stack')->info('🧠 SQL query executed: '.$query->sql, $query->bindings);
                }
            });

            $kernel = app(Kernel::class);

            $kernel->whenRequestLifecycleIsLongerThan(
                CarbonInterval::seconds(4),
                function () {
                    Log::channel('stack')->info('whenRequestLifecycleIsLongerThan: '.request()->url());
                }
            );
        }
    }

    protected function configureReteLimiting(): void
    {
        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(100)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response('Take it easy', Response::HTTP_TOO_MANY_REQUESTS, $headers);
                });
        });
    }
}
