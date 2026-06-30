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

class DeleteProduct
{
    use AsAction;

    public function handle(array $data): Product
    {
        $id = $data['productId'] ?? null;
        $name = $data['productName'] ?? null;

        $validator = Validator::make([
            'productId' => $id,
            'productName' => $name
        ], [
            'productId' => 'required|integer|exists:products,id',
            'productName' => 'required|string|max:255',
        ], [
            'productId.required' => 'Bitte geben Sie eine Produkt-ID ein.',
            'productId.integer' => 'Produkt-ID muss eine gültige Ganzzahl sein.',
            'productId.exists' => 'Das Produkt mit der angegebenen ID existiert nicht.',
            'productName.required' => 'Bitte geben Sie einen Produktnamen ein.',
        ]);

        $validated = $validator->validate();

      
        $product = Product::findOrFail($validated['productId']);

       
        if ($product->name !== $validated['productName']) {
            throw ValidationException::withMessages([
                'productName' => ["Die Produkt-ID '{$validated['productId']}' und der Produktname '{$validated['productName']}' gehören nicht zum selben Produkt. Gespeicherter Name: '{$product->name}'"],
            ]);
        }

      
        Gate::authorize('delete', $product);

       
        return DB::transaction(function () use ($product) {
           
            $productData = clone $product;

            InventoryTransaction::create([
                'product_id' => $product->id,
                'column_name_of_change' => 'all',
                'reason_for_change' => 'Einzelnes Produkt gelöscht',
                'old_value' => $product->name,
                'new_value' => null,
                'user_id' => Auth::id(),
            ]);

            $product->delete();

           
            return $productData;
        });
    }

    /**
     * HTTP-Schnittstelle (API)
     */
    public function asController(Request $request): JsonResponse
    {   
        try {
           
            $id = (int) $request->input('productId');
            $name = (string) $request->input('productName');

            $productData = $this->handle([
                'productId' => $id,
                'productName' => $name
            ]);

            return response()->json([
                'message' => 'Produkt erfolgreich gelöscht.',
                'data' => $productData
            ], 200);

        } catch (AuthorizationException $e) {
            return response()->json(['message' => 'Du bist nicht berechtigt, dieses Produkt zu löschen.'], 403);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Das Produkt wurde nicht gefunden.'], 404);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Die übergebenen Daten sind ungültig.',
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
