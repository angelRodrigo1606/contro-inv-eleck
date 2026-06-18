<?php

it('redirects the root path to login', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});
