<?php

namespace App\Actions;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class GetProductsCountLessTen
{
    use AsAction;

    public function handle(): JsonResponse
    {
        $products = Product::with('category')
            ->where('count', '<', 10)
            ->get();
            
        return response()->json($products);
    }

    public function asController(): JsonResponse
    {
        return $this->handle();
    }
}
