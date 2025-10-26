<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Domain\Catalog\Models\Brand;
use Domain\Catalog\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Application;
use Illuminate\View\Factory;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function __invoke(?Category $category): Factory|View|Application
    {
        $brands = Brand::query()
            ->has('products')
            ->select(['id', 'title'])
            ->get();

        $categories = Category::query()
            ->has('products')
            ->select(['id', 'title', 'slug'])
            ->get();

        $products = Product::query()
            ->select(['id', 'title', 'slug', 'price', 'thumbnail'])
            ->when(request('s'), function (Builder $query) {
                $query->whereFullText(['title', 'text'], request('s'));
            })
            ->when($category?->exists, function (Builder $query) use ($category) {
                $query->whereRelation('categories', 'categories.id', '=', $category->id);
            })
            ->filtered()
            ->sorted()
            ->paginate(6);

        return view('catalog.index', compact('brands', 'categories', 'products', 'category'));
    }
}
