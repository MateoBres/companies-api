<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompanyUpdateTest extends TestCase
{
    use RefreshDatabase;
    
    public function unauthenticated_user_is_not_authorized()
    {
        return true;
    }

    //mancherebbe il test per le autorizzazioni dell'utente

    public function request_must_be_valid()
    {
        return true;
    }

    public function request_succeeds()
    {
        return true;
    }
}
