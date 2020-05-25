<?php

namespace App\Clients;

use App\Stock;
use Illuminate\Support\Facades\Http;

class BestBuy implements Client
{
    public function checkAvailability(Stock $stock): StockStatus
    {
        $results = Http::get($this->endpoint($stock->sku))->json();

        return new StockStatus(
            $results['onlineAvailability'],
            $this->dollarsToCents($results['salePrice'])
        );
    }

    /**
     * @return string
     */
    protected function endpoint($sku): string
    {
        $key = config('services.clients.bestBuy.key');

        $url = "https://api.bestbuy.com/v1/products/{$sku}.json?apiKey={$key}";
        return $url;
    }

    private function dollarsToCents($salePrice)
    {
       return (int)($salePrice * 100);
    }
}
