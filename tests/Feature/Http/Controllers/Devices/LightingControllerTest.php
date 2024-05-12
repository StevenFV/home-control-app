<?php

use App\Models\Devices\Lighting;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(DatabaseTransactions::class);
uses(TestCase::class)->in('Feature');

it('test lighting screen can be rendered to admin user', function () {
    $adminUser = $this->createAdminUser();

    $response = $this->actingAs($adminUser)->get(route('lighting.index'));

    $response->assertStatus(200);
});

it("test user don't have permission to render lighting screen", function () {
    $userWithoutPermission = $this->createUserWithoutPermission();

    $response = $this->actingAs($userWithoutPermission)->get(route('lighting.index'));

    $response->assertStatus(403);
});

it('returns correct lighting data', function () {
    $lighting = Lighting::factory()->create();

    expect($lighting->ieee_address)->toBeString()
        ->and($lighting->friendly_name)->toBeString()
        ->and($lighting->brightness)->toBeInt()
        ->and($lighting->energy)->toBeFloat()
        ->and($lighting->linkquality)->toBeInt()
        ->and($lighting->power)->toBeFloat()
        ->and($lighting->state)->toBeString()
        ->and($lighting->updated_at)->toBeInstanceOf(DateTime::class)
        ->and($lighting->created_at)->toBeInstanceOf(DateTime::class)
        ->and($lighting->id)->toBeInt();
});
