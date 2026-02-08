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
        $product = Product::with('category')->find($productId);
        
        if (!$product) {
            return response()->json([
                'error' => 'Produkt nicht gefunden.',
                'message' => 'Das Produkt mit der ID ' . $productId . ' existiert nicht.'
            ], 404);
        }

        return response()->json($product);
    }
    
    public function handleByName(string $productName): JsonResponse
    {
        $product = Product::with('category')->where('name', $productName)->first();
        
        if (!$product) {
            return response()->json([
                'error' => 'Produkt nicht gefunden.',
                'message' => 'Das Produkt mit dem Namen "' . $productName . '" existiert nicht.'
            ], 404);
        }

        return response()->json($product);
    }
    
    public function handleByIdAndName(int $productId, string $productName): JsonResponse
    {
        $product = Product::with('category')
            ->where('id', $productId)
            ->where('name', $productName)
            ->first();
        
        if (!$product) {
            return response()->json([
                'error' => 'Produkt nicht gefunden.',
                'message' => 'Produkt-ID "' . $productId . '" und Produktname "' . $productName . '" gehören nicht zum selben Produkt oder das Produkt existiert nicht.'
            ], 404);
        }

        return response()->json($product);
    }

    public function asController(int $id): JsonResponse
    {
        return $this->handle($id);
    }
}
