<?php

namespace App\Actions;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

class GetCategory
{
    use AsAction;

    public function handle(array $data): Collection
    {
        $categoryId = $data['categoryId'] ?? null;
        $categoryName = $data['categoryName'] ?? null;

        $validator = Validator::make(
            ['categoryId' => $categoryId, 'categoryName' => $categoryName],
            [
            'categoryId'   => 'required_without:categoryName|integer|nullable',
            'categoryName' => 'required_without:categoryId|string|nullable',
        ],
        [
            'categoryId.required_without' => 'Bitte geben Sie eine Kategorie-ID oder einen Kategorienamen ein.',
            'categoryName.required_without' => 'Bitte geben Sie eine Kategorie-ID oder einen Kategorienamen ein.',
            'categoryId.integer' => 'Kategorie-ID muss eine gültige Ganzzahl sein.',
        ]
    );

    if ($validator->fails()) {
        throw new ValidationException($validator);
    }

    $validated = $validator->validated();

    
    $category = Category::when($validated['categoryId'], function ($query, $id) {
            return $query->where('id', $id);
        }, function ($query) use ($validated) {
            return $query->where('name', $validated['categoryName']);
        })
        ->first();


    if (!$category) {
        $searchKey = $validated['categoryId'] ?: $validated['categoryName'];
        throw ValidationException::withMessages([
            'categoryId' => ["Kategorie mit dem Wert '{$searchKey}' nicht gefunden."],
        ]);
    }

    // Fall 2: Beide Werte wurden übergeben, passen aber nicht zusammen!
    if ($validated['categoryId'] && $validated['categoryName'] && $category->name !== $validated['categoryName']) {
        throw ValidationException::withMessages([
            'categoryName' => ["Kategoriename '{$validated['categoryName']}' stimmt nicht mit der ID {$validated['categoryId']} überein. Gespeicherter Name: '{$category->name}'"],
        ]);
    }

    $category->setAppends([]); 

    $products = $category->products; 
    if ($products->isEmpty()) {
        throw ValidationException::withMessages([
            'categoryId' => ["Keine Produkte in der Kategorie '{$category->name}' (ID: {$category->id}) gefunden."],
        ]);
    }

    return $products; 
    }
    

    public function asController(Request $request, $categoryId, $categoryName = null): JsonResponse
    {
        $category = $this->handle(['categoryId' => $categoryId, 'categoryName' => $categoryName]);

        return response()->json($category);
    }
}
