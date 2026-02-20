<?php
 
namespace App\Actions;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\InventoryTransaction;
use Lorisleiva\Actions\Concerns\AsAction;


class StockAdjustment{
    use AsAction; 

     public function handleIfProdExists(array $StockAdjustmentInfo): ?Product
    {
        $productId = $StockAdjustmentInfo['product_id'];
        try {
            $product = Product::findOrFail($productId);
            return $product;
        } catch (\Exception $e) {
            return null; // Product not found, do not update
        }
    }

     public function handleIfProdNamComplies(Product $product, array $StockAdjustmentInfo): ?Product
    {
        $productName = $StockAdjustmentInfo['product_name'];
    
        if($product->name !== $productName) {
            return null; // Product name does not match, do not update
        }
        return $product; 
    }


    public function handleIfStockNegative(Product $product, array $StockAdjustmentInfo): bool
    {
        $adjustment = $StockAdjustmentInfo['adjustment'];
    
        // Adjust stock count
        $newCount = $product->count + $adjustment;

        // Ensure stock count does not go negative
        if($newCount < 0) {
            return false; // Invalid adjustment leading to negative stock
        }
        $transactionInfo = [
            'product_id' => $product->id,
            'column_name_of_change' => 'count',
            'reason_for_change' => 'Bestandsanpassung',
            'old_value' => $product->count,
            'new_value' => $newCount,
            'user_id' => Auth::id(),
        ];
        InventoryTransaction::create($transactionInfo);

        $product->count = $newCount;
        $product->save();

        return true;
    }

    public function handleIfStockFull(Product $product, array $StockAdjustmentInfo): bool
    {
        $adjustment = $StockAdjustmentInfo['adjustment'];
        
        // Calculate new stock count
        $newCount = $product->count + $adjustment;
        
        // Ensure stock count does not exceed 100
        if($newCount > 100) {
            return false; // Invalid adjustment leading to stock exceeding limit
        }
        
        return true;
    }

    public function handleStockAdjustment(array $data): ?Product
    {
        // Validate input
        Validator::validate($data, [
            'product_id' => 'required|integer',
            'product_name' => 'required|string',
        ]);

        
        DB::beginTransaction();

        // Prüfe ob Produkt existiert
        $prod = $this->handleIfProdExists($data);
        if (!$prod) {
            DB::rollBack();
            throw new \Exception('Produkt mit ID ' . $data['product_id'] . ' nicht gefunden.');
        }

        // Prüfe ob Produktname übereinstimmt
        $prod = $this->handleIfProdNamComplies($prod, $data);
        if (!$prod) {
            DB::rollBack();
            throw new \Exception('Eingegebener Produktname "' . $data['product_name'] . '" stimmt nicht mit dem gespeicherten Produktnamen überein.');
        }

        // Prüfe ob Bestand 100 nicht überschreitet
        if (!$this->handleIfStockFull($prod, $data)) {
            $neuerBestand = $prod->count + $data['adjustment'];
            DB::rollBack();
            throw new \Exception('Bestandsanpassung würde die Obergrenze von 100 überschreiten. Aktueller Bestand: ' . $prod->count . ', Anpassung: ' . $data['adjustment'] . ', Neuer Bestand wäre: ' . $neuerBestand);
        }

        // Prüfe ob Bestand nicht negativ wird
        if (!$this->handleIfStockNegative($prod, $data)) {
            DB::rollBack();
            throw new \Exception('Bestandsanpassung würde zu einem negativen Bestand führen. Aktueller Bestand: ' . $prod->count . ', Anpassung: ' . $data['adjustment']);
        }

        DB::commit();
        return $prod->fresh();
    }

    public function asController(Request $request): JsonResponse
    {
        // Validate input
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_name' => 'required|string',
            'adjustment' => 'required|integer',
        ]);

        $prodInfo = [
            'product_id' => $validated['product_id'],
            'adjustment' => $validated['adjustment'],
            'product_name' => $validated['product_name']
        ];

        // Use database transaction for data consistency
        try {
            DB::beginTransaction();

            $prod = $this->handleIfProdExists($prodInfo);
            if (!$prod) {
                DB::rollBack();
                return response()->json(['message' => 'Stock adjustment failed. Product ID does not exist in stock.'], 400);
            }

            // Prüfe ob Produktname übereinstimmt
            $prod = $this->handleIfProdNamComplies($prod, $prodInfo);
            if (!$prod) {
                DB::rollBack();
                return response()->json(['message' => 'Stock adjustment failed. Product name does not match.'], 400);
            }

            // Check authorization - both admin/manager and staff can adjust stock
            Gate::authorize('adjustStock', $prod);

            if (!$this->handleIfStockFull($prod, $prodInfo)) {
                DB::rollBack();
                return response()->json(['message' => 'Stock adjustment failed. Maximum stock limit of 100 would be exceeded.'], 400);
            }

            if (!$this->handleIfStockNegative($prod, $prodInfo)) {
                DB::rollBack();
                return response()->json(['message' => 'Stock adjustment failed. Adjustment leads to negative stock.'], 400);
            }
            DB::commit();
            $prod_fresh=$prod->fresh();
            return response()->json($prod_fresh, 200);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Stock adjustment failed: ' . $e->getMessage()], 500);
        }
    }
}
