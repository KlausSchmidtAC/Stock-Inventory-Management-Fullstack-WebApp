<?php

namespace App\Actions;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class GetOutOfStockProducts
{
    use AsAction;

    public function handle(): JsonResponse
    {
        $products = Product::with('category')
            ->where('count', 0)
            ->get();
            
        return response()->json($products);
    }

    public function asController(): JsonResponse
    {
        return $this->handle();
    }
}
