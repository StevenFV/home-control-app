<?php

use App\Models\Devices\Lighting;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

it('creates lighting model successfully', function () {
    $lighting = Lighting::factory()->create();

    $this->assertDatabaseHas('devices.lights', [
        'ieee_address' => $lighting->ieee_address,
        'friendly_name' => $lighting->friendly_name,
        'brightness' => $lighting->brightness,
        'energy' => $lighting->energy,
        'linkquality' => $lighting->linkquality,
        'power' => $lighting->power,
        'state' => $lighting->state,
        'created_at' => $lighting->created_at,
        'updated_at' => $lighting->updated_at
    ]);
});

it('updates lighting model successfully', function () {
    $lighting = Lighting::factory()->create();

    sleep(1); // to make sure updated_at is different (1-second delay)

    $newState = $lighting->state === 'on' ? 'off' : 'on';
    $originalUpdatedAt = $lighting->updated_at;

    $lighting->update([
        'state' => $newState
    ]);

    $this->assertTrue($lighting->updated_at > $originalUpdatedAt, "Error: updated_at isn't updated");

    $this->assertDatabaseHas('devices.lights', [
        'ieee_address' => $lighting->ieee_address,
        'state' => $newState
    ]);
});

it('deletes lighting model successfully', function () {
    $lighting = Lighting::factory()->create();

    $lighting->delete();

    $this->assertDatabaseMissing('devices.lights', ['id' => $lighting->id]);
});
