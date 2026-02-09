<?php

namespace App\Actions;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GetCategory
{
    use AsAction;

    public function handle($categoryId): Category
    {
        // Validierung
        $validated = validator(['categoryId' => $categoryId], [
            'categoryId' => 'required|integer',
        ])->validate();

        try{
        $category = Category::findOrFail($validated['categoryId']);
        } 
        catch(ModelNotFoundException $e){
            throw new \Exception('Kategorie mit ID ' . $validated['categoryId'] . ' nicht gefunden.');
        }
        return $category;
    }
    
    public function handleByName($categoryName): Category
    {
        // Validierung
        $validated = validator(['categoryName' => $categoryName], [
            'categoryName' => 'required|string',
        ])->validate();

        $category = Category::where('name', $validated['categoryName'])->first();
        
        if (!$category) {
            throw new \Exception('Kategorie mit Name "' . $validated['categoryName'] . '" existiert nicht.');
        }
        return $category;
    }

    public function asController(Request $request, int $id): JsonResponse
    {
        try{
        $category = Category::findOrFail($id);
        } 
        catch(ModelNotFoundException $e){
            return response()->json([
                'error' => 'Kategorie nicht gefunden.',
                'message' => 'Die Kategorie mit der ID ' . $id . ' existiert nicht.'
            ], 404);
        }

        return response()->json($category);
    }
}
