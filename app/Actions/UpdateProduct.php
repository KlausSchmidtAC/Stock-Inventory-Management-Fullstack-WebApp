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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateProduct
{
    use AsAction;

    public function handle(array $data): Product
    {
        

        $validator = Validator::make($data,[
            'productId' => 'required|integer|exists:products,id',
            'categoryId' => 'required|integer|exists:categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'supplier' => 'required|string|max:255',
            'manufacturer' => 'required|string|max:255',
            'isbn' => ['nullable|string|max:255',
            function ($_, $value, $fail) {
                        if (!empty($value) && !$this->isValidIsbn($value)) {
                            $fail('Die eingegebene ISBN ist ungültig.');
                        }
                    }
            ]
        ], [
            'productId.exists' => 'Das Produkt mit der angegebenen ID existiert nicht.',
            'categoryId.exists' => 'Die ausgewählte Kategorie existiert nicht.',
            'price.min' => 'Bitte einen positiven Betrag als Preis angeben!',
        ]);

       
        $validator->after(function ($validator) use ($data) {
            if (!empty($data['isbn']) && !$this->isValidIsbn($data['isbn'])) {
                $validator->errors()->add('isbn', 'Bitte geben Sie eine gültige 10- oder 13-stellige ISBN-Nummer ein.');
            }
        });

        $validated = $validator->validate();
        $validated['category_id'] = $validated['categoryId'];
        $id = $validated['productId'];
        $product = Product::findOrFail($id);

        
        Gate::authorize('update', $product);

        
        return DB::transaction(function () use ($product, $validated) {
            $product->fill($validated);

            if ($product->isDirty()) {
                $changed = $product->getDirty();
                $oldValues = $product->getOriginal();
                $transactions = [];

                foreach (array_keys($changed) as $column) {
                    // Timestamps ignorieren, falls diese automatisch befüllt wurden
                    if (in_array($column, ['updated_at', 'created_at'])) {
                        continue;
                    }

                    $transactions[] = [
                        'product_id' => $product->id,
                        'column_name_of_change' => $column,
                        'reason_for_change' => 'Produktinformationen aktualisiert',
                        'old_value' => $oldValues[$column] ?? null,
                        'new_value' => $changed[$column],
                        'user_id' => Auth::id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

              
                if (!empty($transactions)) {
                    InventoryTransaction::insert($transactions);
                }

                $product->save();
            }

            return $product->fresh();
        });
    }

    /**
     * HTTP
     */
    public function asController(Request $request, int $id): JsonResponse
    {
        try {
            
            $product = $this->handle(array_merge(['id' => $id], $request->only([
                'name', 'price', 'categoryId', 'supplier', 'manufacturer', 'ISBN'
            ])));

            return response()->json($product, 200);

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json(['message' => 'Du bist nicht berechtigt, dieses Produkt zu bearbeiten.'], 403);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Das Produkt oder die Kategorie wurde nicht gefunden.'], 404);

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

    /**
     * ISBN-Validiation
     */
    private function isValidIsbn(?string $isbn): bool
    {
        if (!$isbn) return false;
        
        $isbn = str_replace(['-', ' '], '', strtoupper($isbn));
        
        if (preg_match('/^\d{9}[\dX]$/', $isbn)) {
            $sum = 0;
            for ($i = 0; $i < 9; $i++) $sum += ((int)$isbn[$i]) * (10 - $i);
            $check = $isbn[9] == 'X' ? 10 : (int)$isbn[9];
            $sum += $check;
            return $sum % 11 == 0;
        } 
        
        if (preg_match('/^97[89]\d{10}$/', $isbn)) {
            $sum = 0;
            for ($i = 0; $i < 12; $i++) $sum += ((int)$isbn[$i]) * ($i % 2 ? 3 : 1);
            $check = (10 - ($sum % 10)) % 10;
            return $check == (int)$isbn[12];
        }

        return false;
    }
}