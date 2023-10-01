<?php

namespace Tests\Unit;

use App\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class BrandControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $adminUser = User::factory()->create(['role' => 'admin']);
        $this->actingAs($adminUser);
    }

    public function testIndexReturnsBrands()
    {
        $response = $this->get(route('admin.brands.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.brands.index');
    }

    public function testCreateBrand()
    {
        $response = $this->post(route('admin.brands.store'), [
            'name' => 'New Brand Name',
        ]);
        $response->assertRedirect(route('admin.brands.index'));
        $this->assertDatabaseHas('brands', [
            'name' => 'New Brand Name',
        ]);
    }

    public function testEditBrand()
    {
        $brand = Brand::factory()->create();
        $response = $this->get(route('admin.brands.edit', $brand->id));
        $response->assertStatus(200);
        $response->assertViewIs('admin.brands.edit');
    }

    public function testUpdateBrand()
    {
        $brand = Brand::factory()->create();
        $response = $this->put(route('admin.brands.update', $brand->id), [
            'name' => 'Updated Brand Name',
        ]);
        $response->assertRedirect(route('admin.brands.index'));
        $this->assertDatabaseHas('brands', [
            'id' => $brand->id,
            'name' => 'Updated Brand Name',
        ]);
    }

    public function testDeleteBrand()
    {
        $brand = Brand::factory()->create();
        $response = $this->delete(route('admin.brands.destroy', $brand->id));
        $response->assertRedirect(route('admin.brands.index'));
        $this->assertDatabaseMissing('brands', [
            'id' => $brand->id,
        ]);
    }
}
