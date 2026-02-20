<?php

namespace App\Actions;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateCategory
{
    use AsAction;

    /**
     * Haupt-Business-Logik: Erstellt eine neue Kategorie
     * Wird sowohl von Livewire (Dashboard) als auch von API-Routen (asController) verwendet
     */
    public function handle(array $data): Category
    {
        // Prüfe ob eine Kategorie mit gleichem Namen bereits existiert

        $validated = validator($data, [
        'name' => 'required|string|max:255',
        ])->validate();


        Gate::authorize('create', Category::class);
        $existingCategory = Category::where('name', $data['name'])->first();
        if ($existingCategory) {
            throw new \Exception('Eine Kategorie mit dem Namen "' . $data['name'] . '" existiert bereits (ID: ' . $existingCategory->id . ').');
        }
        
        return Category::create($validated);
    }

    /**
     * Controller-Methode für API-Routen
     */
    public function asController(Request $request): JsonResponse
    {
        // Check authorization - only admin can create categories
        Gate::authorize('create', Category::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $category = $this->handle($validated);

        return response()->json($category, 201);
    }
}
