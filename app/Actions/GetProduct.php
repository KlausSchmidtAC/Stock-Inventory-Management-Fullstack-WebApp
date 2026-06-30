<?php

namespace App\Actions;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Http\Request; 
use Illuminate\Validation\ValidationException;

class GetProduct
{
    use AsAction;

    public function handle(array $data): Product
    {
        $productId = $data['productId'] ?? null;
        $productName = $data['productName'] ?? null;

        // 1. Validierung (Nur strukturelle Prüfung, keine strenge Namensprüfung via exists)
        $validator = Validator::make(
            ['productId' => $productId, 'productName' => $productName],
            [
                'productId'   => 'required_without:productName|integer|nullable|exists:products,id',
                'productName' => 'required_without:productId|string|nullable',
            ],
            [
                'productId.required_without' => 'Bitte geben Sie eine Produkt-ID oder einen Produktnamen ein.',
                'productName.required_without' => 'Bitte geben Sie eine Produkt-ID oder einen Produktnamen ein.',
                'productId.integer' => 'Produkt-ID muss eine gültige Ganzzahl sein.',
                'productId.exists' => 'Produkt mit der angegebenen ID existiert nicht.',
                'productName.string' => 'Produktname muss eine Zeichenkette sein.',
                'productName.exists' => 'Produkt mit dem angegebenen Namen existiert nicht.',
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $query = Product::with('category');

        if (!empty($productId)) {
        
            $product = $query->where('id', $productId)->first();
            if (!$product) {
                throw ValidationException::withMessages([
                    'productId' => 'Produkt mit der angegebenen ID existiert nicht.',
                ]);
            }
        } elseif (!empty($productName)) {
            $product = $query->where('name', 'LIKE', trim($productName))->first();

            if (!$product) {
                $product = $query->where('name', 'LIKE', '%' . trim($productName) . '%')->first();
            }

            if (!$product) {
                throw ValidationException::withMessages([
                    'productName' => 'Produkt mit dem angegebenen Namen existiert nicht.',
                ]);
            }
        }

        if (!empty($productId) && !empty($productName)) {
          
            if (strcasecmp(trim($product->name), trim($productName)) !== 0) {
                throw ValidationException::withMessages([
                    'productName' => ["Produkt-ID '{$productId}' und Produktname '{$productName}' gehören nicht zum selben Produkt. Gespeicherter Name: '{$product->name}'"],
                ]);
            }
        }


        return $product;
    }

    


    public function asController(Request $request): JsonResponse
    {
        $product = $this->handle($request->only(['productId', 'productName']));
        return response()->json($product);
    }
}
