<?php

namespace App\Actions;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class GetCategories
{
    use AsAction;

    public function handle(array $data = []): Collection
    {
        Gate::authorize('viewAny', Category::class);
        return Category::with([
            'products',
            'productsWithLowStock',
            'productsOutOfStock'
        ])->withCount([
            'products',
            'productsWithLowStock',
            'productsOutOfStock'
        ])->orderBy('name')->get();
    }

    public function asController(): JsonResponse
    {
        try {
            // Ruft die handle-Methode auf
            $categories = $this->handle();

            return response()->json($categories, 200);
        } catch (AuthorizationException $e) {
            return response()->json([
                'message' => 'Du bist nicht berechtigt, die Kategorien anzeigen zu lassen.'
            ], 403);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ein unerwarteter Serverfehler ist aufgetreten.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
