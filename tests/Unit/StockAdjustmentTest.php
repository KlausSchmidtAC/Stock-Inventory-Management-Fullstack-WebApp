<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Actions\StockAdjustment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StockAdjustmentTest extends TestCase
{
    use RefreshDatabase;

    private StockAdjustment $stockAdjustment;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stockAdjustment = new StockAdjustment();
        
        // Erstelle Kategorie und Produkt für Tests
        $category = Category::factory()->create(['name' => 'Test Category']);
        $this->product = Product::factory()->create([
            'name' => 'Test Product',
            'count' => 50,
            'category_id' => $category->id,
        ]);
    }

    /** @test */
    public function it_finds_existing_product_by_id()
    {
        $prodInfo = [
            'product_id' => $this->product->id,
            'product_name' => 'Test Product',
            'adjustment' => 10,
        ];

        $result = $this->stockAdjustment->handleIfProdExists($prodInfo);

        $this->assertNotNull($result);
        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals($this->product->id, $result->id);
    }

    /** @test */
    public function it_returns_null_for_non_existing_product()
    {
        $prodInfo = [
            'product_id' => 99999, // Nicht existierende ID
            'product_name' => 'Non-existing Product',
            'adjustment' => 10,
        ];

        $result = $this->stockAdjustment->handleIfProdExists($prodInfo);

        $this->assertNull($result);
    }

    /** @test */
    public function it_validates_product_name_matches()
    {
        $prodInfo = [
            'product_id' => $this->product->id,
            'product_name' => 'Test Product',
            'adjustment' => 10,
        ];

        $result = $this->stockAdjustment->handleIfProdNamComplies($this->product, $prodInfo);

        $this->assertNotNull($result);
        $this->assertEquals($this->product->id, $result->id);
    }

    /** @test */
    public function it_returns_null_when_product_name_does_not_match()
    {
        $prodInfo = [
            'product_id' => $this->product->id,
            'product_name' => 'Wrong Product Name',
            'adjustment' => 10,
        ];

        $result = $this->stockAdjustment->handleIfProdNamComplies($this->product, $prodInfo);

        $this->assertNull($result);
    }

    /** @test */
    public function it_allows_positive_stock_adjustment()
    {
        $prodInfo = [
            'product_id' => $this->product->id,
            'product_name' => 'Test Product',
            'adjustment' => 20,
        ];

        $result = $this->stockAdjustment->handleIfStockNegative($this->product, $prodInfo);

        $this->assertTrue($result);
        $this->assertEquals(70, $this->product->fresh()->count);
    }

    /** @test */
    public function it_allows_negative_stock_adjustment_within_bounds()
    {
        $prodInfo = [
            'product_id' => $this->product->id,
            'product_name' => 'Test Product',
            'adjustment' => -30,
        ];

        $result = $this->stockAdjustment->handleIfStockNegative($this->product, $prodInfo);

        $this->assertTrue($result);
        $this->assertEquals(20, $this->product->fresh()->count);
    }

    /** @test */
    public function it_prevents_negative_stock_edge_case_exact_zero()
    {
        $this->product->update(['count' => 50]);
        
        $prodInfo = [
            'product_id' => $this->product->id,
            'product_name' => 'Test Product',
            'adjustment' => -50, // Genau auf 0
        ];

        $result = $this->stockAdjustment->handleIfStockNegative($this->product, $prodInfo);

        $this->assertTrue($result);
        $this->assertEquals(0, $this->product->fresh()->count);
    }

    /** @test */
    public function it_prevents_negative_stock_edge_case_minus_one()
    {
        $this->product->update(['count' => 50]);
        
        $prodInfo = [
            'product_id' => $this->product->id,
            'product_name' => 'Test Product',
            'adjustment' => -51, // Würde -1 ergeben
        ];

        $result = $this->stockAdjustment->handleIfStockNegative($this->product, $prodInfo);

        $this->assertFalse($result);
        $this->assertEquals(50, $this->product->fresh()->count); // Keine Änderung
    }

    /** @test */
    public function it_prevents_large_negative_stock_adjustment()
    {
        $this->product->update(['count' => 10]);
        
        $prodInfo = [
            'product_id' => $this->product->id,
            'product_name' => 'Test Product',
            'adjustment' => -100, // Viel zu groß
        ];

        $result = $this->stockAdjustment->handleIfStockNegative($this->product, $prodInfo);

        $this->assertFalse($result);
        $this->assertEquals(10, $this->product->fresh()->count); // Keine Änderung
    }

    /** @test */
    public function it_allows_stock_adjustment_to_exactly_100()
    {
        $this->product->update(['count' => 80]);
        
        $prodInfo = [
            'product_id' => $this->product->id,
            'product_name' => 'Test Product',
            'adjustment' => 20, // Ergibt genau 100
        ];

        $result = $this->stockAdjustment->handleIfStockFull($this->product, $prodInfo);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_prevents_stock_exceeding_100_edge_case()
    {
        $this->product->update(['count' => 100]);
        
        $prodInfo = [
            'product_id' => $this->product->id,
            'product_name' => 'Test Product',
            'adjustment' => 1, // Würde 101 ergeben
        ];

        $result = $this->stockAdjustment->handleIfStockFull($this->product, $prodInfo);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_prevents_large_stock_increase_exceeding_limit()
    {
        $this->product->update(['count' => 50]);
        
        $prodInfo = [
            'product_id' => $this->product->id,
            'product_name' => 'Test Product',
            'adjustment' => 51, // Würde 101 ergeben
        ];

        $result = $this->stockAdjustment->handleIfStockFull($this->product, $prodInfo);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_handles_stock_at_boundary_99_to_100()
    {
        $this->product->update(['count' => 99]);
        
        $prodInfo = [
            'product_id' => $this->product->id,
            'product_name' => 'Test Product',
            'adjustment' => 1,
        ];

        $result = $this->stockAdjustment->handleIfStockFull($this->product, $prodInfo);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_handles_stock_at_boundary_1_to_0()
    {
        $this->product->update(['count' => 1]);
        
        $prodInfo = [
            'product_id' => $this->product->id,
            'product_name' => 'Test Product',
            'adjustment' => -1,
        ];

        $result = $this->stockAdjustment->handleIfStockNegative($this->product, $prodInfo);

        $this->assertTrue($result);
        $this->assertEquals(0, $this->product->fresh()->count);
    }

    /** @test */
    public function it_prevents_stock_from_0_to_negative()
    {
        $this->product->update(['count' => 0]);
        
        $prodInfo = [
            'product_id' => $this->product->id,
            'product_name' => 'Test Product',
            'adjustment' => -1,
        ];

        $result = $this->stockAdjustment->handleIfStockNegative($this->product, $prodInfo);

        $this->assertFalse($result);
        $this->assertEquals(0, $this->product->fresh()->count);
    }

    /** @test */
    public function it_allows_negative_adjustment_with_stock_full_check()
    {
        $this->product->update(['count' => 100]);
        
        $prodInfo = [
            'product_id' => $this->product->id,
            'product_name' => 'Test Product',
            'adjustment' => -10, // Negative Anpassung von voller Grenze
        ];

        $result = $this->stockAdjustment->handleIfStockFull($this->product, $prodInfo);

        $this->assertTrue($result); // Sollte erlaubt sein, da es runtergeht
    }
}
