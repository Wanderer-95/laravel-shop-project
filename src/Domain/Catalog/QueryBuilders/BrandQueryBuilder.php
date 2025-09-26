<?php

namespace Domain\Catalog\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;

class BrandQueryBuilder extends Builder
{
    public function homePage(): Builder
    {
        return $this->select(['id', 'slug', 'title', 'thumbnail', 'on_home_page', 'sorting'])
            ->where('on_home_page', true)
            ->orderBy('sorting')
            ->limit(6);
    }
}
