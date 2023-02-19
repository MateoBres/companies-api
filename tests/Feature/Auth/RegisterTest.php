<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    // preparazione dati per i test
    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->make();
    }


    // TEST FUNZIONALI
    public function test_registrazione_ok()
    {
        $this->postJson(route('user.register'), [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'password' => $this->user->password,
            'password_confirmation' => $this->user->password
        ])
            ->assertJsonStructure(['data'])
            ->assertCreated() // verifica risposta inserimento 201
            ->json();

        $this->assertDatabaseHas('users', ['name' => $this->user->name]);
    }

    // TEST DI VALIDAZIONE
    public function test_email_formato_non_valido()
    {
        $this->postJson(route('user.register'), [
            'name' => $this->user->name,
            'email' => 'matteo.bresciani_gmail.com',
            'password' => $this->user->password,
            'password_confirmation' => $this->user->password
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(1, ['errors'])
            ->assertStatus(422);  //verifica risposta errore validazione - unprocessable         
    }


    public function test_name_email_e_password_validazione_obbligatori()
    {
        $this->postJson(route('user.register'), [
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => ''
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(4, ['errors'])
            ->assertStatus(422);
    }


    public function test_conferma_password_fallita()
    {
        $this->postJson(route('user.register'), [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'password' => $this->user->password,
            'password_confirmation' => '123456'
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(1, ['errors'])
            ->assertStatus(422);
    }


    public function test_password_e_conferma_password_validazione_minimo_8_caratteri()
    {
        $this->postJson(route('user.register'), [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'password' => '123456',
            'password_confirmation' => '123456'
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(2, ['errors'])
            ->assertStatus(422);
    }


    public function test_email_già_registrata()
    {
        $this->postJson(route('user.register'), [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'password' => $this->user->password,
            'password_confirmation' => $this->user->password
        ]);

        $this->postJson(route('user.register'), [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'password' => $this->user->password,
            'password_confirmation' => $this->user->password
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(1, ['errors'])
            ->assertStatus(422);
    }
}
