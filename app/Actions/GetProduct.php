<?php

namespace App\Actions;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;
use \Illuminate\Http\Request; 

class GetProduct
{
    use AsAction;

    public function handle($productId): Product
    {
        // Validierung
        $validated = validator(['productId' => $productId], [
            'productId' => 'required|integer',
        ])->validate();

        $product = Product::with('category')->find($validated['productId']);
        
        if (!$product) {
            throw new \Exception('Das Produkt mit der ID ' . $validated['productId'] . ' existiert nicht.');
        }

        return $product;
    }
    
    public function handleByName($productName): Product
    {
        // Validierung
        $validated = validator(['productName' => $productName], [
            'productName' => 'required|string',
        ])->validate();

        $product = Product::with('category')->where('name', $validated['productName'])->first();
        
        if (!$product) {
            throw new \Exception('Das Produkt mit dem Namen "' . $validated['productName'] . '" existiert nicht.');
        }

        return $product;
    }
    
    public function handleByIdAndName($productId, $productName): Product
    {
        // Validierung
        $validated = validator(['productId' => $productId, 'productName' => $productName], [
            'productId' => 'required|integer',
            'productName' => 'required|string',
        ])->validate();

        $product = Product::with('category')
            ->where('id', $validated['productId'])
            ->where('name', $validated['productName'])
            ->first();
        
        if (!$product) {
            throw new \Exception('Produkt-ID "' . $validated['productId'] . '" und Produktname "' . $validated['productName'] . '" gehören nicht zum selben Produkt oder das Produkt existiert nicht.');
        }

        return $product;
    }

    public function asController(Request $request, int $id): JsonResponse
    {
        $product = $this->handle($id);
        return response()->json($product);
    }
}
