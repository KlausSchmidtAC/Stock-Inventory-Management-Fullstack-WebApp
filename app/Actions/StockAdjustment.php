<?php

namespace App\Actions;

use App\Models\Product;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class StockAdjustment
{
    use AsAction;
    public function handle(array $data): Product
    {

        $validator = Validator::make(
            $data,
            [
                'productId' => 'required|integer|exists:products,id',
                'productName' => 'required|string|max:255',

                'adjustment' => ['required', 'string', 'regex:/^[+\-]\d+$/'],
            ],
            [
                'productId.exists' => 'Das Produkt mit der angegebenen ID existiert nicht.',
                'adjustment.regex' => 'Die Bestandsanpassung muss mit + oder - beginnen, gefolgt von einer positiven Ganzzahl (z.B. +10 oder -5).',
            ]
        );

        $validated = $validator->validate();

        $product = Product::findOrFail($validated['productId']);


        if ($product->name !== $validated['productName']) {
            throw ValidationException::withMessages([
                'productName' => ["Der eingegebene Produktname '{$validated['productName']}' stimmt nicht mit dem gespeicherten Produktnamen überein."],
            ]);
        }


        Gate::authorize('adjustStock', $product);


        $adjustmentValue = (int)$validated['adjustment'];
        $newCount = $product->count + $adjustmentValue;


        if ($newCount > 100) {
            throw ValidationException::withMessages([
                'adjustment' => ["Bestandsanpassung würde die Obergrenze von 100 überschreiten. Aktueller Bestand: {$product->count}, Anpassung: {$validated['adjustment']}, Neuer Bestand wäre: {$newCount}"],
            ]);
        }


        if ($newCount < 0) {
            throw ValidationException::withMessages([
                'adjustment' => ["Bestandsanpassung würde zu einem negativen Bestand führen. Aktueller Bestand: {$product->count}, Anpassung: {$validated['adjustment']}"],
            ]);
        }


        return DB::transaction(function () use ($product, $adjustmentValue, $newCount) {


            InventoryTransaction::create([
                'product_id' => $product->id,
                'column_name_of_change' => 'count',
                'reason_for_change' => 'Bestandsanpassung',
                'old_value' => $product->count,
                'new_value' => $newCount,
                'user_id' => Auth::id(),
            ]);

            // Bestand aktualisieren
            $product->count = $newCount;
            $product->save();

            return $product->fresh();
        });
    }

    /**
     * HTTP-API
     */
    public function asController(Request $request): JsonResponse
    {
        try {

            $data = $request->only(['productId', 'productName', 'adjustment']);


            $product = $this->handle($data);

            return response()->json($product, 200);
        } catch (AuthorizationException $e) {
            return response()->json([
                'message' => 'Du bist nicht berechtigt, den Bestand dieses Produkts anzupassen.'
            ], 403);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Das Produkt wurde nicht gefunden.'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Die Bestandsanpassung ist fehlgeschlagen.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ein unerwarteter Serverfehler ist aufgetreten.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
