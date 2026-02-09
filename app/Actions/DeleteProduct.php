<?php

namespace App\Actions;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DeleteProduct
{
    use AsAction;

    public function handle($id): bool
    {
        // Validierung
        $validated = validator(['productId' => $id], [
            'productId' => 'required|integer',
        ])->validate();

         try{
        $product = Product::findorfail($validated['productId']);
        }
        catch (ModelNotFoundException $e) {
            throw new \Exception('Produkt mit ID ' . $validated['productId'] . ' nicht gefunden.');
        }

        // Check authorization - only admin/manager can delete
        Gate::authorize('delete', $product);
        return $product->delete();
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
        
        // Check authorization - only admin/manager can delete
        Gate::authorize('delete', $product);

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully', 'data' => $product], 200);
    }
}
