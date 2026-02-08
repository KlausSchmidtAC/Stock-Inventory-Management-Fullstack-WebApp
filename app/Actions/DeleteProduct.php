<?php

namespace App\Actions;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteProduct
{
    use AsAction;

    public function handle(Product $product): bool
    {
        return $product->delete();
    }

    public function asController(Request $request, int $id): JsonResponse
    {
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json([
                'error' => 'Produkt nicht gefunden.',
                'message' => 'Das Produkt mit der ID ' . $id . ' existiert nicht.'
            ], 404);
        }

        // Check authorization - only admin/manager can delete
        Gate::authorize('delete', $product);

        $this->handle($product);

        return response()->json(['message' => 'Product deleted successfully', 'data' => $product], 200);
    }
}
