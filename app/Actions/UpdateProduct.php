<?php

namespace App\Actions;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UpdateProduct
{
    use AsAction;

    public function handle($id, array $data): Product
    {
        // Validierung
        $validated = validator(array_merge(['id' => $id], $data), [
            'id' => 'required|integer',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|integer',
            'supplier' => 'required|string|max:255',
            'manufacturer' => 'required|string|max:255',
            'isbn' => 'sometimes|string|max:255',
        ])->validate();

        try{
        $product = Product::findorfail($validated['id']);
        }
        catch (ModelNotFoundException $e) {
            throw new \Exception('Produkt mit ID ' . $validated['id'] . ' nicht gefunden.');
        }

        // Check authorization - only admin/manager can update
        Gate::authorize('update', $product);

        $product->update($data);
        return $product->fresh();
    }

    public function asController(Request $request, int $id): JsonResponse
    {
        try{
        $product = Product::findorfail($id);
        }
        catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Produkt nicht gefunden.',
                'message' => 'Das Produkt mit der ID ' . $id . ' existiert nicht.'
            ], 404);
        }

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

        $product->update($validated);
        $updatedProduct = $product->fresh();

        return response()->json($updatedProduct);
    }
}
