<?php

namespace App\Actions;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Models\InventoryTransaction;

class DeleteCategory
{
    use AsAction;

    /**
     * Delete a category and all its products
     */
    public function handle(int $categoryId): JsonResponse
    {
        /** @var Category|null $category */
        $category = Category::find($categoryId);

        if (!$category) {
            return response()->json([
                'message' => 'Kategorie nicht gefunden.',
                'category_id' => $categoryId
            ], 404);
        }
        Gate::authorize('delete', $category);
        // Count products before deletion
        $products = $category->products(); 
        $productsCount = $category->products()->count();
        $categoryName = $category->name;


        foreach ($products->get() as $product) {
            $transaction_info_prods = [
                'product_id' => $product->id,
                'column_name_of_change' => 'all',
                'reason_for_change' => 'Produkt gelöscht (Kategorie '.$category->name.' mit ID '.$category->id.' gelöscht)',
                'old_value' => $product->name,
                'new_value' => null,
                'user_id' => Auth::id(),
            ];
            InventoryTransaction::create($transaction_info_prods);
        }

        // Delete all products in this category first
        $category->products()->delete();

        // Delete the category
        $category->delete();

        return response()->json([
            'message' => 'Kategorie und alle enthaltenen Produkte wurden erfolgreich gelöscht.',
            'category_id' => $categoryId,
            'category_name' => $categoryName,
            'deleted_products_count' => $productsCount
        ], 200);
    }

    /**
     * Handle HTTP request
     */
    public function asController(int $id): JsonResponse
    {
        return $this->handle($id);
    }
}
