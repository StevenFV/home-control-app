<?php

namespace Tests\Feature\Http\Controllers\Devices;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LightingControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_lighting_controller_construct_can_be_initialise_correctly()
    {
        // Code to test the constructor of the LightingController
    }

    public function test_lighting_index_screen_can_be_rendered(): void
    {
        // Create a factory class to return user with different roles
        // It will be used in different tests

        $response = $this->get('/devices/lighting');

        $response->assertStatus(200);
    }
}