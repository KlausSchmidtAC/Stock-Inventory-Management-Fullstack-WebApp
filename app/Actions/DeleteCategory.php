<?php

namespace App\Actions;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;

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

        // Count products before deletion
        $productsCount = $category->products()->count();
        $categoryName = $category->name;

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
