<?php

use App\Models\User;
use Livewire\Livewire;
use Modules\Products\Livewire\ProductForm;
use Modules\Products\Livewire\ProductIndex;
use Modules\Products\Models\Product;
use Modules\Products\Models\ProductCategory;
use Modules\Users\Models\Role;

function makeProductsRole(): Role
{
    return Role::query()->create([
        'name' => 'Products Admin '.str()->random(6),
        'can_view' => true,
        'can_create' => true,
        'can_edit' => true,
        'can_delete' => true,
        'can_export' => true,
        'record_visibility' => 'all',
        'module_access' => ['products' => true, 'users' => true, 'core' => true],
    ]);
}

test('authorized users can open products index page', function () {
    $role = makeProductsRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $this->actingAs($user)
        ->get(route('products.index'))
        ->assertOk()
        ->assertSee('Products');
});

test('users can create a product from livewire form', function () {
    $role = makeProductsRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $category = ProductCategory::query()->create([
        'name' => 'Software',
    ]);

    $this->actingAs($user);

    Livewire::test(ProductForm::class)
        ->set('name', 'Growth Suite')
        ->set('sku', 'SKU-GROWTH1')
        ->set('description', 'Monthly growth suite')
        ->set('unit_price', '199')
        ->set('cost_price', '80')
        ->set('currency', 'USD')
        ->set('category_id', $category->id)
        ->set('tax_rate', '21')
        ->set('active', true)
        ->set('recurring', true)
        ->set('billing_frequency', 'Monthly')
        ->set('unit', 'licenses')
        ->call('save');

    $this->assertDatabaseHas('products', [
        'name' => 'Growth Suite',
        'sku' => 'SKU-GROWTH1',
        'category_id' => $category->id,
        'recurring' => true,
        'billing_frequency' => 'Monthly',
    ]);
});

test('users can toggle product active status from index', function () {
    $role = makeProductsRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $category = ProductCategory::query()->create([
        'name' => 'Hardware',
    ]);

    $product = Product::query()->create([
        'name' => 'Scanner Kit',
        'sku' => 'SKU-SCAN01',
        'unit_price' => 250,
        'cost_price' => 150,
        'currency' => 'USD',
        'category_id' => $category->id,
        'tax_rate' => 21,
        'active' => true,
        'recurring' => false,
        'billing_frequency' => 'One-time',
        'unit' => 'units',
    ]);

    $this->actingAs($user);

    Livewire::test(ProductIndex::class)
        ->call('toggleActive', $product->id);

    expect($product->fresh()->active)->toBeFalse();
});
