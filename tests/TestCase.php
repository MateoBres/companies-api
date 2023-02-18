<?php

namespace Tests;

use App\Models\User;
use App\Models\Company;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;


    public function setUp(): void
    {
        parent::setUp();
    }

    public function createCompany($args = [])
    {
        return Company::factory()->create($args);
    }

    public function createUser($args = [])
    {
        return User::factory()->create($args);
    }

    public function authUser()
    {
        $user = $this->createUser();
        
        // Sanctum si occupa del login e della gestione del token
        Sanctum::actingAs($user);

        return $user;
    }
}