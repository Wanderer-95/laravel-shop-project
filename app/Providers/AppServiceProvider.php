<?php

namespace App\Providers;

use Carbon\CarbonInterval;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
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
}
