<?php

namespace App\Actions;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class GetProduct
{
    use AsAction;

    public function handle(int $productId): JsonResponse
    {
        $product = Product::with('category')->findOrFail($productId);
        
        return response()->json($product);
    }

    public function asController(int $id): JsonResponse
    {
        return $this->handle($id);
    }
}
