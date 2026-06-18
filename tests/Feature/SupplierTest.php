<?php

use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;

describe('suppliers', function () {
    beforeEach(function () {
        $this->admin = User::factory()->admin()->create();
        $this->employee = User::factory()->employee()->create();
    });

    it('allows admin to view suppliers list', function () {
        Supplier::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('suppliers.index'));

        $response->assertOk()
            ->assertSee('Listado de proveedores');
    });

    it('forbids employee to view suppliers list', function () {
        $response = $this->actingAs($this->employee)->get(route('suppliers.index'));

        $response->assertForbidden();
    });

    it('allows admin to create a supplier', function () {
        $data = [
            'name' => 'Proveedor SA',
            'contact_name' => 'Juan Pérez',
            'phone' => '555-1234',
            'address' => 'Calle 123',
            'email' => 'proveedor@example.com',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)->post(route('suppliers.store'), $data);

        $response->assertRedirect(route('suppliers.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('suppliers', ['name' => 'Proveedor SA']);
    });

    it('allows admin to update a supplier', function () {
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($this->admin)
            ->put(route('suppliers.update', $supplier), [
                'name' => 'Actualizado',
                'contact_name' => $supplier->contact_name,
                'phone' => $supplier->phone,
                'address' => $supplier->address,
                'email' => $supplier->email,
                'is_active' => true,
            ]);

        $response->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id, 'name' => 'Actualizado']);
    });

    it('allows admin to delete a supplier without products', function () {
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('suppliers.destroy', $supplier));

        $response->assertRedirect(route('suppliers.index'))
            ->assertSessionHas('success');
        $this->assertSoftDeleted($supplier);
    });

    it('prevents admin from deleting a supplier with products', function () {
        $supplier = Supplier::factory()->create();
        Product::factory()->create(['supplier_id' => $supplier->id]);

        $response = $this->actingAs($this->admin)
            ->delete(route('suppliers.destroy', $supplier));

        $response->assertRedirect(route('suppliers.index'))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id]);
    });
});
