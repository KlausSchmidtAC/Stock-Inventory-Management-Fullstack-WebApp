<?php

namespace App\Actions;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CreateCategory
{
    use AsAction;

    /**
     * Haupt-Business-Logik: Erstellt eine neue Kategorie
     * Wird sowohl von Livewire (Dashboard) als auch von API-Routen (asController) verwendet
     */
    public function handle(array $data): Category
    {
         // Check authorization - only admin can create categories
        Gate::authorize('create', Category::class);

        // Prüfe ob eine Kategorie mit gleichem Namen bereits existiert   
        $validator = Validator::make(
            ['categoryName' => $data['categoryName'] ?? null], 
            [
            'categoryName' => 'required|string|max:255|unique:categories,name',
            ],
        
        [
            'categoryName.required' => 'Bitte geben Sie einen Kategorienamen ein.',
            'categoryName.string' => 'Kategoriename muss eine Zeichenkette sein.',
            'categoryName.max' => 'Kategoriename darf nicht länger als 255 Zeichen sein.',
            'categoryName.unique' => 'Eine Kategorie mit diesem Namen existiert bereits. ', ]);  // opt. Angabe der ID dieser Kategorie (ID: :id)
        
        $validated = $validator->validate();
        $validated['name'] = $validated['categoryName'];

        if(!$validated) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }
        
        return Category::create($validated);
    }

    /**
     * Controller-Methode für API-Routen
     */
    public function asController(Request $request): JsonResponse
    {
        
        $category = $this->handle($request->only(['categoryName']));
        return response()->json($category, 201);
    }
}
