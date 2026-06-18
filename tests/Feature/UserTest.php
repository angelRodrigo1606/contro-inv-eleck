<?php

use App\Models\User;

describe('users', function () {
    beforeEach(function () {
        $this->admin = User::factory()->admin()->create();
        $this->employee = User::factory()->employee()->create();
    });

    it('allows admin to view users list', function () {
        $response = $this->actingAs($this->admin)->get(route('users.index'));
        $response->assertOk()->assertSee('Listado de usuarios');
    });

    it('forbids employee to view users list', function () {
        $response = $this->actingAs($this->employee)->get(route('users.index'));
        $response->assertForbidden();
    });

    it('allows admin to create a user', function () {
        $data = [
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo@example.com',
            'role' => 'empleado',
            'password' => 'password123',
        ];

        $response = $this->actingAs($this->admin)->post(route('users.store'), $data);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', ['email' => 'nuevo@example.com', 'role' => 'empleado']);
    });

    it('prevents admin from deleting own account', function () {
        $response = $this->actingAs($this->admin)
            ->delete(route('users.destroy', $this->admin));

        $response->assertRedirect(route('users.index'))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    });
});
