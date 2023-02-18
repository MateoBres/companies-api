<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompanyCrudNoAutorizationTest extends TestCase
{
    use RefreshDatabase;

    // preparazione dati per i test
    private $company;

    public function setUp(): void
    {
        parent::setUp();

        $this->company =  $this->createCompany([
            'businessName' => 'my company',
            'vat' => '12345678901',
            'taxCode' => 'BRSMTT78S14B157O',
            'type' => 4
        ]);
    }

    // TESTS OPERAZIONI CRUD UTENTE NON UTORIZZATO (SOLO HA ECCESSO ALL'INDEX)

    public function test_richiesta_tutte_le_company_utente_non_utorizzato()
    {
        $this->getJson(route('company.index'))
            ->assertJsonStructure(['data', 'meta'])
            ->assertOk();
    }

    public function test_richiesta_di_una_company_utente_non_utorizzato()
    {
        $this->getJson(route('company.show', $this->company->id))
            ->assertJsonStructure(['message'])
            ->assertUnauthorized();
    }

    public function test_salvataggio_nuova_company_utente_non_utorizzato()
    {
        $company = Company::factory()->make(); // non salva a db   

        $this->postJson(route('company.store'), ['businessName' => $company->businessName, 'vat' => $company->vat, 'taxCode' => $company->taxCode, 'type' => $company->type])
            ->assertJsonStructure(['message'])
            ->assertUnauthorized();
    }

    public function test_aggiornamento_dati_company_utente_non_utorizzato()
    {
        $this->patchJson(route('company.update', $this->company->id), ['businessName' => 'updated businessName'])
            ->assertJsonStructure(['message'])
            ->assertUnauthorized();
    }

    public function test_eliminazione_company_utente_non_utorizzato()
    {
        $this->deleteJson(route('company.destroy', $this->company->id))
            ->assertJsonStructure(['message'])
            ->assertUnauthorized();
    }
}