<?php

namespace Tests\Unit;

use App\Clients\StockStatus;
use App\Product;
use Facades\App\Clients\ClientFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RetailerWithProductSeeder;
use Tests\TestCase;

class ProductHistoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_records_history_each_time_a_stock_is_tracked()
    {
        $this->seed(RetailerWithProductSeeder::class);

        $this->mockClientRequest($available = true, $price = 9900);

        $product = tap(Product::first(), function($product){
            $this->assertCount(0, $product->history);

            $product->track();

            $this->assertCount(1, $product->refresh()->history);
        });

        $history = $product->history->first();
        $this->assertEquals($price,$history->price);
        $this->assertEquals($available,$history->in_stock);
        $this->assertEquals($product->id,$history->product_id);
        $this->assertEquals($product->stock[0]->id,$history->stock_id);
    }
}
