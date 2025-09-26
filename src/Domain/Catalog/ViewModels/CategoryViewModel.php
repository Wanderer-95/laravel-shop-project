<?php

namespace Domain\Catalog\ViewModels;

use Domain\Catalog\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Support\Traits\MakeableTrait;

class CategoryViewModel
{
    use MakeableTrait;

    public function homePage(): Collection
    {
        return Cache::rememberForever('category_home_page', function () {
            return Category::query()->homePage()->get();
        });
    }
}
