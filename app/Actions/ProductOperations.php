<?php

namespace App\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use Lorisleiva\Actions\Concerns\AsAction;

class ProductOperations
{
    use AsAction;
    
    public function GetAllWithCategoryhandle(int $categoryId)
    {
        $products = Product::with('category')->where('category_id', $categoryId)->get();
        return response()->json($products);        
    }
    
    public function asController(Request $request, int $id)
    {
        return $this->GetAllWithCategoryhandle($id);
    }


}