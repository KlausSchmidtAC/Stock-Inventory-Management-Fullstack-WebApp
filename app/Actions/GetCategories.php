<?php

namespace App\Actions;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class GetCategories
{
    use AsAction;

    public function handle(): Collection
    {
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
}