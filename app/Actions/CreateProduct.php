<?php

namespace App\Actions;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateProduct
{
    use AsAction;

    public function handle(array $data): Product
    {
        return Product::create($data);
    }

    public function asController(Request $request): JsonResponse
    {
        // Check authorization - only admin/manager can create
        Gate::authorize('create', Product::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'isbn' => 'nullable|string|max:255',
            'count' => 'required|integer|min:0',
            'manufacturer' => 'nullable|string|max:255',
            'supplier' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'last_supplied_at' => 'nullable|date',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product = $this->handle($validated);

        return response()->json($product, 201);
    }
}
