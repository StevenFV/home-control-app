<?php

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

it('test login screen can be rendered', function () {
    $this->get(route('login'))->assertStatus(200);
});

it('test admin users can authenticate and access dashboard page', function () {
    $user = User::factory()->assignAdminRole()->create();

    $this->actingAs($user)->get(route('dashboard'))
        ->assertStatus(200);
});

it('test users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->assertInvalidCredentials([
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);
});
