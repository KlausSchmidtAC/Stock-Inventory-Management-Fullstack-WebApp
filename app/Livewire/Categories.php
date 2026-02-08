<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Category;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
class Categories extends Component
{
    public function mount()
    {
        // Nur authentifizierte Benutzer dürfen diese Seite sehen
        Gate::authorize('viewAny', Category::class);
    }

    public function render()
    {
        $categories = Category::withCount(['products', 'productsWithLowStock', 'productsOutOfStock'])
            ->orderBy('name')
            ->get();

        return view('livewire.categories', [
            'categories' => $categories
        ]);
    }
}
