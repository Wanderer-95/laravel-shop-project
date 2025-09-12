<?php

namespace App\Routing;

use App\Contracts\RouteRegistrarContract;
use App\Http\Controllers\Home\HomeController;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Support\Facades\Route;

class AppRegistrar implements RouteRegistrarContract
{
    public function map(Registrar $registrar): void
    {
        Route::middleware('web')->group(function () {
            Route::get('/', HomeController::class)->name('home');
        });
    }
}
