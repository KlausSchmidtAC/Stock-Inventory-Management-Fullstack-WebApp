<?php

namespace App\Actions;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateProduct
{
    use AsAction;

    public function handle(Product $product, array $data): Product
    {
        $product->update($data);
        return $product->fresh();
    }

    public function asController(Request $request, int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        // Check authorization - only admin/manager can update
        Gate::authorize('update', $product);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'isbn' => 'nullable|string|max:255',
            'count' => 'sometimes|integer|min:0',
            'manufacturer' => 'nullable|string|max:255',
            'supplier' => 'nullable|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'last_supplied_at' => 'nullable|date',
            'category_id' => 'sometimes|exists:categories,id',
        ]);

        $updatedProduct = $this->handle($product, $validated);

        return response()->json($updatedProduct);
    }
}
