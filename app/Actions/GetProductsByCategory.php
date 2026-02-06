<?php

namespace App\Actions;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class GetProductsByCategory
{
    use AsAction;

    public function handle(int $categoryId): JsonResponse
    {
        $products = Product::with('category')
            ->where('category_id', $categoryId)
            ->get();
            
        return response()->json($products);
    }

    public function asController(int $id): JsonResponse
    {
        return $this->handle($id);
    }
}
