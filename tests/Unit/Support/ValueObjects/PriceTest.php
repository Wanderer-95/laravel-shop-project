<?php

namespace Support\ValueObjects;

use InvalidArgumentException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceTest extends TestCase
{
    use RefreshDatabase;

    public function test_all()
    {
        $price = new Price(100);

        $this->assertInstanceOf(Price::class, $price);
        $this->assertEquals(100, $price->raw());
        $this->assertEquals(1, $price->value());
        $this->assertEquals('RUB', $price->currency());
        $this->assertEquals('Р', $price->symbol());
        $this->assertEquals('1 Р', $price);

        $this->expectException(InvalidArgumentException::class);

        Price::make(-100);
        Price::make(100, 'USD');
    }
}
