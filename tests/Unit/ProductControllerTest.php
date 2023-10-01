<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\User;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $adminUser = User::factory()->create(['role' => 'admin']);
        $this->actingAs($adminUser);
    }

    public function testProductCanBeCreated()
    {
        $this->withoutMiddleware();

        Storage::fake('testing');

        $category = Category::factory()->create();
        $brand = Brand::factory()->create();

        $data = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 19.99,
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'image' => UploadedFile::fake()->image('test.jpg'),
        ];

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('admin.products.store'), $data);

        $response->assertRedirect(route('admin.products.index'))
            ->assertSessionHas('success', 'Product has been added.');

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 19.99,
            'category_id' => $category->id,
            'brand_id' => $brand->id,
        ]);

        Storage::disk('testing')->assertMissing($data['image']->hashName());
    }

    public function testProductCanBeSuccessfullyUpdated()
    {
        $this->withoutMiddleware();

        Storage::fake('testing');

        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();

        $data = [
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'category_id' => $product->category_id,
            'brand_id' => $product->brand_id,
        ];

        if (isset($data['image'])) {
            $data['image'] = UploadedFile::fake()->image('updated.jpg');
        }

        $expectedFields = array_keys($data);

        $response = $this->put(route('admin.products.update', $product), $data);

        $response->assertRedirect(route('admin.products.index'))
            ->assertSessionHas('success', 'Product has been updated.');

        $updatedProduct = Product::find($product->id);

        foreach ($expectedFields as $field) {
            $this->assertEquals($data[$field], $updatedProduct->$field);
        }

        if (isset($data['image'])) {
            Storage::disk('testing')->assertMissing($data['image']->hashName());
        } else {
            $this->assertEquals($product->image, $updatedProduct->image);
        }
    }

    public function testCanDeleteProduct()
    {
        $this->withoutMiddleware();

        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();

        $response = $this->delete(route('admin.products.destroy', $product));

        $response->assertRedirect(route('admin.products.index'))
            ->assertSessionHas('success', 'Product has been deleted.');

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }
}
