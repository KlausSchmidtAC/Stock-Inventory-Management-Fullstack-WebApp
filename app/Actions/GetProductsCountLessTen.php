<?php

namespace App\Actions;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class GetProductsCountLessTen
{
    use AsAction;

    public function handle(): Collection
    {
        $products = Product::with('category')
            ->where('count', '<', 10)
            ->get();
        
        if($products->isEmpty()) {
            throw ValidationException::withMessages([
                'products' => ["Es gibt keine Produkte mit weniger als 10 Stück. Alle Produkte sind gut bevorratet!"],
            ]);
        }

        return $products;
    }

    public function asController(): JsonResponse
    {
        return response()->json($this->handle());
    }
}
