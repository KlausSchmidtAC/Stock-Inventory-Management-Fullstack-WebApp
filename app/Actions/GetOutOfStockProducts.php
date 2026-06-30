<?php

namespace App\Actions;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Database\Eloquent\Collection; 
use Illuminate\Validation\ValidationException;

class GetOutOfStockProducts
{
    use AsAction;

    public function handle(): Collection
    {

        $products = Product::with('category')
            ->where('count', 0)
            ->get();

        if($products->isEmpty()) {
            throw ValidationException::withMessages([
                'products' => ["Es gibt keine Produkte, die derzeit ausverkauft sind. Alle Produkte sind auf Lager!"],
            ]);
        }

        return $products;
    }

    public function asController(): JsonResponse
    {
        return response()->json($this->handle());
    }
}
