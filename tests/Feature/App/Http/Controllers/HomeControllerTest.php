<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_displays_correct_data(): void
    {
        // Arrange: создаём по 3 записи каждой сущности для главной страницы
        $brands = Brand::factory()->count(3)->create(['on_home_page' => true]);
        $categories = Category::factory()->count(3)->create(['on_home_page' => true]);
        $products = Product::factory()->count(3)->create(['on_home_page' => true]);

        // Act: отправляем GET-запрос на главную страницу
        $response = $this->get(route('home')); // Убедись, что маршрут назван `home`

        // Assert: проверяем успешный ответ
        $response->assertOk();

        // Проверяем, что используется нужное представление
        $response->assertViewIs('app');

        // Проверяем, что во вьюху переданы переменные
        $response->assertViewHasAll([
            'categories',
            'products',
            'brands',
        ]);

        // Проверяем содержимое данных (по id, чтобы было надёжно)
        $viewCategories = $response->viewData('categories');
        $viewProducts = $response->viewData('products');
        $viewBrands = $response->viewData('brands');

        $this->assertCount(3, $viewCategories);
        $this->assertCount(3, $viewProducts);
        $this->assertCount(3, $viewBrands);

        $this->assertEqualsCanonicalizing(
            $categories->pluck('id')->toArray(),
            $viewCategories->pluck('id')->toArray()
        );

        $this->assertEqualsCanonicalizing(
            $products->pluck('id')->toArray(),
            $viewProducts->pluck('id')->toArray()
        );

        $this->assertEqualsCanonicalizing(
            $brands->pluck('id')->toArray(),
            $viewBrands->pluck('id')->toArray()
        );
    }
}
