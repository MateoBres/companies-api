<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;


    // preparazione dati per i test
    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser(); // factory gia presente in laravel
    }

    // TESTS FUNZIONALI
    public function test_login_ok()
    {
        $response = $this->postJson(route('user.login'), [
            'email' => $this->user->email,
            'password' => '1234567890'
        ])
            ->assertJsonStructure(['data', 'token'])
            ->assertOk();

        // verifichiamo che gli sia stato assegnato un token
        $this->assertArrayHasKey('token', $response->json());
    }

    public function test_password_sbagliata()
    {

        $this->postJson(route('user.login'), [
            'email' => $this->user->email,
            'password' => 'invalid_random_password'
        ])
            ->assertJsonStructure(['errors'])
            ->assertUnauthorized();
    }


    public function test_email_non_registrata()
    {
        $email = User::factory()->fakeEmail();

        $this->postJson(route('user.login'), [
            'email' => $email['email'],
            'password' => $this->user->password
        ])
            ->assertJsonStructure(['errors'])
            ->assertUnauthorized();
    }

    // TEST VALIDAZIONI
    public function test_email_e_password_validazione_obbligatori()
    {
        $this->postJson(route('user.login'), [
            'email' => '',
            'password' => ''
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(2, ['errors'])
            ->assertUnauthorized();
    }


    public function test_email_formato_non_valido()
    {
        $email = User::factory()->fakeEmail();

        // rendo invalida l'email genarata dalla factory
        $invalidEmail = str_replace('@', '_', $email);

        $this->postJson(route('user.login'), [
            'email' => $invalidEmail,
            'password' => $this->user->password
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(1, ['errors'])
            ->assertUnauthorized();
    }
}
