<?php

namespace App\Actions;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Models\InventoryTransaction;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DeleteCategory
{
    use AsAction;

    /**
     * Delete a category and all its products
     */
    public function handle(array $data): bool
    {
        $validator = Validator::make(['categoryName' => $data['categoryName']], [
            'categoryName' => 'required|string|max:255',
        ], [
            'categoryName.required' => 'Der Kategoriename ist erforderlich.',
            'categoryName.string' => 'Der Kategoriename muss eine Zeichenkette sein.',
            'categoryName.max' => 'Der Kategoriename darf maximal 255 Zeichen lang sein.',
            'categoryName.exists' => 'Die Kategorie mit dem angegebenen Namen existiert nicht.',

        ]);

        $validated = $validator->validate();

        $category = Category::with('products')->where('name', $validated['categoryName'])->firstOrFail();
        if ($validated['categoryName'] && $category->name !== $validated['categoryName']) {
            throw ValidationException::withMessages([
                'categoryName' => ["Kategoriename '{$validated['categoryName']}' gehört nicht zur selben Kategorie. Gespeicherter Name: '{$category->name}'"],
            ]);
        }
         Gate::authorize('delete', $category);

        return DB::transaction(function () use ($category) {

            $products = $category->products;

            $transactions = [];
            foreach ($products as $product) {
                $transactions[] = [
                    'product_id' => $product->id,
                    'column_name_of_change' => 'all',
                    'reason_for_change' => "Produkt gelöscht (Kategorie {$category->name} mit ID {$category->id} gelöscht)",
                    'old_value' => $product->name,
                    'new_value' => null,
                    'user_id' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if (!empty($transactions)) {
                InventoryTransaction::insert($transactions);
            }
            $category->products()->delete();
            return $category->delete();
        });
    }

    /**
     * Handle HTTP request
     */
    public function asController(Request $request): JsonResponse
    {
        try {
            $deleted = (bool) $this->handle($request->only(['categoryName']));
            if ($deleted) {
                return response()->json([
                    'message' => 'Kategorie und alle enthaltenen Produkte wurden erfolgreich gelöscht.',
                    'categoryName' => $request->input('categoryName')
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Kategorie konnte nicht gelöscht werden.',
                    'categoryName' => $request->input('categoryName')
                ], 400);
            }
        } catch (AuthorizationException $e) {
            return response()->json([
                'message' => 'Keine Berechtigung zum Löschen der Kategorie.',
                'categoryName' => $request->input('categoryName')
            ], 403);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Die angeforderte Kategorie existiert nicht.'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Die übergebenen Daten sind ungültig.',
                'errors' => $e->errors()
            ], 422); 
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ein unerwarteter Fehler ist aufgetreten.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
