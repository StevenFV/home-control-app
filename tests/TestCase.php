<?php

namespace Tests;

use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function createUserWithoutPermission()
    {
        return UserFactory::new()
            ->create();
    }

    protected function createAdminUser()
    {
        return UserFactory::new()
            ->assignAdminRole()
            ->create();
    }
}
