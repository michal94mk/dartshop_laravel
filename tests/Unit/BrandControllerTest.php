<?php

namespace Tests\Unit;

use App\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class BrandControllerTest extends TestCase
{
    use RefreshDatabase;
    public function setUp(): void
    {
        parent::setUp();

        // Create admin role
        \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        
        $adminUser = User::factory()->create(['is_admin' => true]);
        $adminUser->assignRole('admin');
        $this->actingAs($adminUser);
    }

    public function testIndexReturnsBrands()
    {
        $response = $this->getJson('/api/admin/brands');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data', 'pagination' => ['current_page', 'total']]);
    }

    public function testCreateBrand()
    {
        $response = $this->postJson('/api/admin/brands', [
            'name' => 'New Brand Name',
        ]);
        $response->assertStatus(201);
        $response->assertJsonStructure(['success', 'message', 'data']);
        $this->assertDatabaseHas('brands', [
            'name' => 'New Brand Name',
        ]);
    }

    public function testEditBrand()
    {
        $brand = Brand::factory()->create();
        $response = $this->getJson("/api/admin/brands/{$brand->id}");
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data' => ['id', 'name']]);
    }

    public function testUpdateBrand()
    {
        $brand = Brand::factory()->create();
        $response = $this->putJson("/api/admin/brands/{$brand->id}", [
            'name' => 'Updated Brand Name',
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'message', 'data']);
        $this->assertDatabaseHas('brands', [
            'id' => $brand->id,
            'name' => 'Updated Brand Name',
        ]);
    }

    public function testDeleteBrand()
    {
        $brand = Brand::factory()->create();
        $response = $this->deleteJson("/api/admin/brands/{$brand->id}");
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);
        $this->assertDatabaseMissing('brands', [
            'id' => $brand->id,
        ]);
    }
}
