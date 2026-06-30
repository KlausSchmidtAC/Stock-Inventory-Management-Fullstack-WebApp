<?php

namespace App\Livewire;

use Livewire\Component;
use App\Actions\GetProductsByCategory;
use App\Actions\GetProduct;
use App\Actions\GetCategory;
use App\Actions\GetOutOfStockProducts;
use App\Actions\GetProductsCountLessTen;
use App\Actions\CreateProduct;
use App\Actions\CreateCategory;
use App\Actions\UpdateProduct;
use App\Actions\DeleteProduct;
use App\Actions\DeleteCategory;
use App\Actions\GetCategories;
use App\Actions\StockAdjustment;
use Livewire\Attributes\Layout;
use PhpParser\Node\Stmt\ClassLike;


#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    public string $selectedAction = '';

    public $showCategoriesComponent = false;


    public array $formData = [
        'productId' => '',
        'categoryId' => '',
        'categoryName' => '',
        'categoryNameForCreate' => '',
        'name' => '',
        'productName' => '',
        'price' => '',
        'categoryIdForCreate' => '',
        'stockQuantity' => '',
        'adjustment' => '',
        'ISBN' => '',
        'supplier' => '',
        'manufacturer' => '',
    ];

    // Response data
    public $result = null;
    public $error = null;

    protected array $actionMethodsMapping = [
        'get-by-category' => 'handleGetByCategory',
        'get-product' => 'handleGetProduct',
        'out-of-stock' => 'handleOutOfStockProducts',
        'low-stock-products' => 'handleLowStockProducts',
        'create-product' => 'handleCreateProduct',
        'create-category' => 'handleCreateCategory',
        'delete-category' => 'handleDeleteCategory',
        'update-product' => 'handleUpdateProduct',
        'delete-product' => 'handleDeleteProduct',
        'stock-adjustment' => 'handleStockAdjustment',
        'view-categories' => 'handleViewCategories',
    ];

    public function mount()
    {
        if (request()->has('selectedAction') && request()->has('categoryId')) {
            $this->selectedAction = request()->get('selectedAction', '');
            $categoryIdFromRequest = request()->get('categoryId');
            $categoryNameFromRequest = request()->get('categoryName');


            if ($this->selectedAction === 'get-by-category' && is_numeric($categoryIdFromRequest)) {
                $this->formData['categoryId'] = $categoryIdFromRequest;
                $this->formData['categoryName'] = $categoryNameFromRequest;
                $this->executeAction();
            }
        } else {

            $this->reset(['result', 'error']);
        }
    }

    public function updatedSelectedAction()
    {
        $this->reset(['result','error']);
        $this->formData = array_map(fn($value) => '', $this->formData);
    }

    public function executeAction()
    {
        $this->reset(['result', 'error']);

        if (!array_key_exists($this->selectedAction, $this->actionMethodsMapping)) {
            $this->error = 'Bitte wählen Sie eine gültige Aktion aus.';
            return;
        }

        try{

        $methodName = $this->actionMethodsMapping[$this->selectedAction];
        $this->result = app()->call([$this, $methodName], $this->formData);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->error = 'Validierungsfehler: ' . collect($e->errors())->flatten()->implode(', ');
        } catch (\Exception $e) {
            $this->error = 'Fehler: ' . $e->getMessage();
        }
    }

    public function handleGetByCategory(\App\Actions\GetCategory $action){
        return $action->handle($this->formData);
        }


    public function handleGetProduct(\App\Actions\GetProduct $action){
        return $action->handle($this->formData);
    }

    public function handleOutOfStockProducts(\App\Actions\GetOutOfStockProducts $action){
        return $action->handle();
    }

    public function handleLowStockProducts(\App\Actions\GetProductsCountLessTen $action){
        return $action->handle();
    }                
    
    public function handleCreateProduct(\App\Actions\CreateProduct $action){
        return $action->handle($this->formData);
    }

    public function handleCreateCategory(\App\Actions\CreateCategory $action){
        return $action->handle($this->formData);
    }

    public function handleDeleteCategory(\App\Actions\DeleteCategory $action){
        return $action->handle($this->formData);
    }

    public function handleUpdateProduct(\App\Actions\UpdateProduct $action){
        return $action->handle($this->formData);
    }

    public function handleDeleteProduct(\App\Actions\DeleteProduct $action){
        return $action->handle($this->formData);
    }

    public function handleStockAdjustment(\App\Actions\StockAdjustment $action){
        return $action->handle($this->formData);
    }

    public function handleViewCategories(\App\Actions\GetCategories $action){
        return $action->handle();
    }

    public function showCategories()
    {
        $this->showCategoriesComponent = !$this->showCategoriesComponent;
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
