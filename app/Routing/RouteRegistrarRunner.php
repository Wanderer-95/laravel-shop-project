<?php

namespace App\Routing;

use App\Contracts\RouteRegistrarContract;
use Illuminate\Contracts\Routing\Registrar;
use RuntimeException;

class RouteRegistrarRunner
{
    protected array $registrars = [
        AppRegistrar::class,
        AuthRegistrar::class,
    ];

    public function __invoke(Registrar $router): void
    {
        $this->mapRoutes($router, $this->registrars);
    }

    protected function mapRoutes(Registrar $router, array $registrars): void
    {
        foreach ($registrars as $registrar) {
            if (! class_exists($registrar) || ! in_array(RouteRegistrarContract::class, class_implements($registrar))) {
                throw new RuntimeException(sprintf(
                    'Cannot map routes "%s", it is not a valid RouteRegistrarContract',
                    $registrar
                ));
            }

            (new $registrar)->map($router);
        }
    }
}
