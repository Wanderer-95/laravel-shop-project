<?php

namespace Domain\Catalog\ViewModels;

use Domain\Catalog\Models\Brand;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Support\Traits\MakeableTrait;

class BrandViewModel
{
    use MakeableTrait;

    public function homePage(): Collection
    {
        return Cache::rememberForever('brand_home_page', function () {
            return Brand::query()->homePage()->get();
        });
    }
}
