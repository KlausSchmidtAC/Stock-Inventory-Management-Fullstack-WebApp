<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StockAdjustmentFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $staff;
    private Category $category;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@test.com',
        ]);
        
        $this->staff = User::factory()->create([
            'role' => 'staff',
            'email' => 'staff@test.com',
        ]);
        
        $this->category = Category::factory()->create(['name' => 'Electronics']);
        
        $this->product = Product::factory()->create([
            'name' => 'Laptop Dell XPS 15',
            'count' => 50,
            'price' => 1500.00,
            'category_id' => $this->category->id,
        ]);
    }

    /** @test */
    public function stock_adjustment_endpoint_prevents_negative_stock()
    {
        $this->actingAs($this->staff);
        
        $response = $this->postJson('/products/stockOperation', [
            'product_id' => $this->product->id,
            'product_name' => 'Laptop Dell XPS 15',
            'adjustment' => -51,
        ]);
        
        $response->assertStatus(400);
        $response->assertJson([
            'message' => 'Stock adjustment failed. Adjustment leads to negative stock.'
        ]);
        
        $this->assertEquals(50, $this->product->fresh()->count);
    }

    /** @test */
    public function stock_adjustment_endpoint_prevents_exceeding_maximum_of_100()
    {
        $this->actingAs($this->staff);
        
        $this->product->update(['count' => 90]);
        
        $response = $this->postJson('/products/stockOperation', [
            'product_id' => $this->product->id,
            'product_name' => 'Laptop Dell XPS 15',
            'adjustment' => 11,
        ]);
        
        $response->assertStatus(400);
        $response->assertJson([
            'message' => 'Stock adjustment failed. Maximum stock limit of 100 would be exceeded.'
        ]);
        
        $this->assertEquals(90, $this->product->fresh()->count);
    }

    /** @test */
    public function stock_adjustment_allows_exactly_100_items()
    {
        $this->actingAs($this->staff);
        
        $this->product->update(['count' => 80]);
        
        $response = $this->postJson('/products/stockOperation', [
            'product_id' => $this->product->id,
            'product_name' => 'Laptop Dell XPS 15',
            'adjustment' => 20,
        ]);
        
        $response->assertStatus(200);
        $this->assertEquals(100, $this->product->fresh()->count);
    }

    /** @test */
    public function stock_adjustment_allows_exactly_0_items()
    {
        $this->actingAs($this->staff);
        
        $this->product->update(['count' => 30]);
        
        $response = $this->postJson('/products/stockOperation', [
            'product_id' => $this->product->id,
            'product_name' => 'Laptop Dell XPS 15',
            'adjustment' => -30,
        ]);
        
        $response->assertStatus(200);
        $this->assertEquals(0, $this->product->fresh()->count);
    }

    /** @test */
    public function stock_adjustment_prevents_going_below_0_from_0()
    {
        $this->actingAs($this->staff);
        
        $this->product->update(['count' => 0]);
        
        $response = $this->postJson('/products/stockOperation', [
            'product_id' => $this->product->id,
            'product_name' => 'Laptop Dell XPS 15',
            'adjustment' => -1,
        ]);
        
        $response->assertStatus(400);
        $this->assertEquals(0, $this->product->fresh()->count);
    }

    /** @test */
    public function stock_adjustment_prevents_going_above_100_from_100()
    {
        $this->actingAs($this->staff);
        
        $this->product->update(['count' => 100]);
        
        $response = $this->postJson('/products/stockOperation', [
            'product_id' => $this->product->id,
            'product_name' => 'Laptop Dell XPS 15',
            'adjustment' => 1,
        ]);
        
        $response->assertStatus(400);
        $this->assertEquals(100, $this->product->fresh()->count);
    }

    /** @test */
    public function stock_adjustment_fails_when_product_name_does_not_match()
    {
        $this->actingAs($this->staff);
        
        $response = $this->postJson('/products/stockOperation', [
            'product_id' => $this->product->id,
            'product_name' => 'Wrong Product Name',
            'adjustment' => 10,
        ]);
        
        $response->assertStatus(400);
        $response->assertJson([
            'message' => 'Stock adjustment failed. Product name does not match.'
        ]);
    }

    /** @test */
    public function stock_adjustment_fails_when_product_does_not_exist()
    {
        $this->actingAs($this->staff);
        
        $response = $this->postJson('/products/stockOperation', [
            'product_id' => 99999,
            'product_name' => 'Non-existing Product',
            'adjustment' => 10,
        ]);
        
        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The selected product id is invalid.'
        ]);
    }

    /** @test */
    public function stock_adjustment_succeeds_with_valid_positive_adjustment()
    {
        $this->actingAs($this->staff);
        
        $response = $this->postJson('/products/stockOperation', [
            'product_id' => $this->product->id,
            'product_name' => 'Laptop Dell XPS 15',
            'adjustment' => 20,
        ]);
        
        $response->assertStatus(200);
        $this->assertEquals(70, $this->product->fresh()->count);
    }

    /** @test */
    public function stock_adjustment_succeeds_with_valid_negative_adjustment()
    {
        $this->actingAs($this->staff);
        
        $response = $this->postJson('/products/stockOperation', [
            'product_id' => $this->product->id,
            'product_name' => 'Laptop Dell XPS 15',
            'adjustment' => -20,
        ]);
        
        $response->assertStatus(200);
        $this->assertEquals(30, $this->product->fresh()->count);
    }

    /** @test */
    public function creating_product_prevents_stock_exceeding_100()
    {
        $this->actingAs($this->admin);
        
        $response = $this->postJson('/products', [
            'name' => 'New Product',
            'price' => 100.00,
            'category_id' => $this->category->id,
            'count' => 101,
        ]);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['count']);
    }

    /** @test */
    public function creating_product_allows_exactly_100_items()
    {
        $this->actingAs($this->admin);
        
        $response = $this->postJson('/products', [
            'name' => 'New Product Max Stock',
            'price' => 100.00,
            'category_id' => $this->category->id,
            'count' => 100,
        ]);
        
        $response->assertStatus(201);
        
        $createdProduct = Product::where('name', 'New Product Max Stock')->first();
        $this->assertEquals(100, $createdProduct->count);
    }

    /** @test */
    public function creating_product_allows_0_items()
    {
        $this->actingAs($this->admin);
        
        $response = $this->postJson('/products', [
            'name' => 'New Product No Stock',
            'price' => 100.00,
            'category_id' => $this->category->id,
            'count' => 0,
        ]);
        
        $response->assertStatus(201);
        
        $createdProduct = Product::where('name', 'New Product No Stock')->first();
        $this->assertEquals(0, $createdProduct->count);
    }

    /** @test */
    public function boundary_test_stock_adjustment_from_1_to_0()
    {
        $this->actingAs($this->staff);
        
        $this->product->update(['count' => 1]);
        
        $response = $this->postJson('/products/stockOperation', [
            'product_id' => $this->product->id,
            'product_name' => 'Laptop Dell XPS 15',
            'adjustment' => -1,
        ]);
        
        $response->assertStatus(200);
        $this->assertEquals(0, $this->product->fresh()->count);
    }

    /** @test */
    public function boundary_test_stock_adjustment_from_99_to_100()
    {
        $this->actingAs($this->staff);
        
        $this->product->update(['count' => 99]);
        
        $response = $this->postJson('/products/stockOperation', [
            'product_id' => $this->product->id,
            'product_name' => 'Laptop Dell XPS 15',
            'adjustment' => 1,
        ]);
        
        $response->assertStatus(200);
        $this->assertEquals(100, $this->product->fresh()->count);
    }

    /** @test */
    public function unauthorized_user_cannot_adjust_stock()
    {
        $response = $this->postJson('/products/stockOperation', [
            'product_id' => $this->product->id,
            'product_name' => 'Laptop Dell XPS 15',
            'adjustment' => 10,
        ]);
        
        $response->assertStatus(401);
    }
}
