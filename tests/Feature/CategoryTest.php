<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;

describe('categories', function () {
    beforeEach(function () {
        $this->admin = User::factory()->admin()->create();
        $this->employee = User::factory()->employee()->create();
    });

    it('allows admin to view categories list', function () {
        Category::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('categories.index'));

        $response->assertOk()
            ->assertSee('Listado de categorías');
    });

    it('forbids employee to view categories list', function () {
        $response = $this->actingAs($this->employee)->get(route('categories.index'));

        $response->assertForbidden();
    });

    it('allows admin to create a category', function () {
        $data = [
            'name' => 'Componentes',
            'description' => 'Componentes electrónicos',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)->post(route('categories.store'), $data);

        $response->assertRedirect(route('categories.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('categories', ['name' => 'Componentes']);
    });

    it('allows admin to update a category', function () {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)
            ->put(route('categories.update', $category), [
                'name' => 'Actualizado',
                'description' => $category->description,
                'is_active' => true,
            ]);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Actualizado']);
    });

    it('allows admin to delete a category without products', function () {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('categories.destroy', $category));

        $response->assertRedirect(route('categories.index'))
            ->assertSessionHas('success');
        $this->assertSoftDeleted($category);
    });

    it('prevents admin from deleting a category with products', function () {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($this->admin)
            ->delete(route('categories.destroy', $category));

        $response->assertRedirect(route('categories.index'))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    });
});
