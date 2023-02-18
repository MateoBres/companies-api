<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompanyCrudTest extends TestCase
{
    use RefreshDatabase;

    // preparazione dati per i test
    private $company;

    public function setUp(): void
    {
        parent::setUp();

        $this->company =  $this->createCompany(['businessName' => 'my company', 'vat' => 12345678901, 'taxCode' => 'BRSMTT78S14B157O', 'type' => 4]);

        $this->authUser();
    }

    // TESTS PER OPERAZIONI CRUD

    // INDEX
    public function test_richiesta_tutte_le_company()
    {
        $response = $this->getJson(route('company.index'))
            ->assertJsonStructure(['data', 'meta']) //verifico la struttura della risposta
            ->assertOk();

        $this->assertEquals('my company', $response->json()['data'][0]['businessName']);
    }

    // SHOW
    public function test_richiesta_di_una_company()
    {
        $response = $this->getJson(route('company.show', $this->company->id))
            ->assertJsonStructure(['data'])
            ->assertOk();

        //ci assicuriamo che la company ritornata sia la stessa che abbiamo creato
        $this->assertEquals($response->json()['data']['businessName'], $this->company->businessName);
    }

    public function test_richiesta_di_una_company_fallita_per_id_non_travato()
    {
        $this->getJson(route('company.show', 0), ['businessName' => 'updated businessName'])
            ->assertJsonStructure(['errors'])
            ->assertStatus(422);
    }

    // STORE
    public function test_salvataggio_nuova_company()
    {
        $company = Company::factory()->make();

        $this->postJson(route('company.store'), [
            'businessName' => $company->businessName, 
            'vat' => $company->vat, 
            'taxCode' => $company->taxCode, 
            'type' => $company->type
            ])
            ->assertJsonStructure(['data'])
            ->assertCreated() //verifica risposta inserimento 201
            ->json();

        $this->assertDatabaseHas('companies', ['businessName' => $company->businessName]);
    }

    // UPDATE
    public function test_aggiornamento_dati_company()
    {
        $this->patchJson(route('company.update', $this->company->id), ['businessName' => 'updated businessName'])
            ->assertJsonStructure(['data'])
            ->assertOk(); //verifica risposta 200

        $this->assertDatabaseHas('companies', ['id' => $this->company->id, 'businessName' => 'updated businessName']);
    }


    public function test_aggiornamento_dati_company_fallita_per_id_non_trovato()
    {
        $this->patchJson(route('company.update', 0), ['businessName' => 'updated businessName'])
            ->assertJsonStructure(['errors'])
            ->assertStatus(422);
    }

    // DESTROY
    public function test_eliminazione_company()
    {
        $this->deleteJson(route('company.destroy', $this->company->id))
            ->assertStatus(204);

        $this->assertDatabaseMissing('companies', ['name' => $this->company->businessName]);
    }

    public function test_eliminazione_company_fallita_per_id_non_trovato()
    {
        $this->deleteJson(route('company.destroy', 0), ['businessName' => 'updated businessName'])
            ->assertJsonStructure(['errors'])
            ->assertStatus(422);
    }
}