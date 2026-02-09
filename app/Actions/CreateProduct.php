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

    /**
     * Haupt-Business-Logik: Erstellt ein neues Produkt
     * Wird sowohl von Livewire (Dashboard) als auch von API-Routen (asController) verwendet
     */
    public function handle(array $data): Product
    {

        $validated = validator($data, [
            'name' => 'required|string|max:255',
            'isbn' => 'sometimes|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'supplier' => 'nullable|string|max:255',
            'stock_quantity' => 'required|integer|min:0|max:100',
            'price' => 'required|numeric|min:0',
            'last_supplied_at' => 'nullable|date',
            'category_id' => 'required|exists:categories,id',
        ])->validate();


        // Prüfe ob ein Produkt mit gleichem Namen bereits existiert
        $existingProduct = Product::where('name', $validated['name'])->first();
        if ($existingProduct) {
            throw new \Exception('Ein Produkt mit dem Namen "' . $validated['name'] . '" existiert bereits (ID: ' . $existingProduct->id . ').');
        }
        
        // Normalisiere die Eingabedaten (falls aus unterschiedlichen Quellen)
        $stockQuantity = $validated['count'] ?? $validated['stock_quantity'] ?? 0;
        
        // Prüfe Obergrenze von 100
        if ($stockQuantity > 100) {
            throw new \Exception('Anfangsbestand darf die Obergrenze von 100 nicht überschreiten.');
        }
        
        $productData = [
            'name' => $validated['name'],
            'price' => $validated['price'],
            'category_id' => $validated['category_id'],
            'count' => $stockQuantity,
            'isbn' => $validated['isbn'] ?? null,
            'manufacturer' => $validated['manufacturer'] ?? null,
            'supplier' => $validated['supplier'] ?? null,
            'last_supplied_at' => $validated['last_supplied_at'] ?? null,
        ];
        
        return Product::create($productData);
    }

    /**
     * Controller-Methode für API-Routen
     */
    public function asController(Request $request): JsonResponse
    {
        // Check authorization - only admin/manager can create
        Gate::authorize('create', Product::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'isbn' => 'nullable|string|max:255',
            'count' => 'required|integer|min:0|max:100',
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
