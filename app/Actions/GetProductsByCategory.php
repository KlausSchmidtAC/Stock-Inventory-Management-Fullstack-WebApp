<?php

namespace App\Actions;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Database\Eloquent\Collection;

class GetProductsByCategory
{
    use AsAction;

    public function handle(array $data): Collection
    {
        $categoryId = $data['category_id'] ?? null;

        $products = Product::with('category')
            ->where('category_id', $categoryId)
            ->get();
            
        return $products;
    }

    public function asController(int $id): JsonResponse
    {
        return response()->json($this->handle(['category_id' => $id]));
    }
}
