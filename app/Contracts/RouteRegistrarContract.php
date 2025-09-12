<?php

namespace App\Contracts;

use Illuminate\Contracts\Routing\Registrar;

interface RouteRegistrarContract
{
    public function map(Registrar $registrar): void;
}
