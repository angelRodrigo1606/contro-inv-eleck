<?php

use App\Models\User;

it('renders the theme anti-fouc script', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('dashboard'));

    $response
        ->assertOk()
        ->assertSee('document.documentElement.classList.toggle(\'dark\'', false);
});

it('renders the theme toggle button for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('dashboard'));

    $response
        ->assertOk()
        ->assertSee('Cambiar tema', false);
});

it('does not render the theme toggle button for guests', function () {
    $response = $this->get(route('login'));

    $response
        ->assertOk()
        ->assertDontSee('Cambiar tema', false);
});
