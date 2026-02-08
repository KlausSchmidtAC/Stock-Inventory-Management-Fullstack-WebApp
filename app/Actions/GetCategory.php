<?php

namespace App\Actions;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class GetCategory
{
    use AsAction;

    public function handle(int $categoryId): JsonResponse
    {
        $category = Category::find($categoryId);
        
        if (!$category) {
            return response()->json([
                'error' => 'Kategorie nicht gefunden.',
                'message' => 'Die Kategorie mit der ID ' . $categoryId . ' existiert nicht.'
            ], 404);
        }

        return response()->json($category);
    }
    
    public function handleByName(string $categoryName): JsonResponse
    {
        $category = Category::where('name', $categoryName)->first();
        
        if (!$category) {
            return response()->json([
                'error' => 'Kategorie nicht gefunden.',
                'message' => 'Die Kategorie mit dem Namen "' . $categoryName . '" existiert nicht.'
            ], 404);
        }

        return response()->json($category);
    }

    public function asController(int $id): JsonResponse
    {
        return $this->handle($id);
    }
}
