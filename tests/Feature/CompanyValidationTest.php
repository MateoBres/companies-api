<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Http\Controllers\CompanyController;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompanyValidationTest extends TestCase
{
    use RefreshDatabase;

    // preparazione dati per i test
    private $company;
    private $rules;

    public function setUp(): void
    {
        parent::setUp();

        $this->rules = new CompanyController();
        $this->company =  $this->createCompany(['businessName' => 'my company', 'vat' => '12345678901', 'taxCode' => 'BRSMTT78S14B157O', 'type' => 4]);

        $this->authUser();
    }

    // TESTS DI VALIDAZIONE

    //UPDATE
    public function test_almeno_un_campo_not_null_durante_update()
    {
        $this->patchJson(route('company.update', $this->company->id), [
            'businessName' => '',
            'address' => '',
            'vat' => '',
            'taxCode' => '',
            'employees' => '',
            'active' => '',
            'type' => ''
        ])
            ->assertJsonStructure(['errors'])
            ->assertStatus(422); // verifica risposta errore validazione                  
    }


    // VALIDAZIONE CAMPI

    // ADDRESS
    public function test_address_string_and_nullable()
    {
        $this->postJson(route('company.store'), [
            'businessName' => $this->company->businessName,
            'address' => '',
            'vat' => $this->company->vat,
            'taxCode' => $this->company->taxCode,
            'type' => $this->company->type
        ])
            ->assertJsonStructure(['data'])
            ->assertCreated() //verifica risposta inserimento 201
            ->json();

        $this->postJson(route('company.store'), [
            'businessName' => $this->company->businessName,
            'address' => 123456,
            'vat' => $this->company->vat,
            'taxCode' => $this->company->taxCode,
            'type' => $this->company->type
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(1, ['errors'])
            ->assertStatus(422); // verifica risposta errore validazione        
    }


    public function test_employees_numeric_and_nullable()
    {
        $this->postJson(route('company.store'), [
            'businessName' => $this->company->businessName,
            'employees' => '',
            'vat' => $this->company->vat,
            'taxCode' => $this->company->taxCode,
            'type' => $this->company->type
        ])
            ->assertJsonStructure(['data'])
            ->assertCreated() //verifica risposta inserimento 201
            ->json();

        $this->postJson(route('company.store'), [
            'businessName' => $this->company->businessName,
            'employees' => 'abc',
            'vat' => $this->company->vat,
            'taxCode' => $this->company->taxCode,
            'type' => $this->company->type
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(1, ['errors'])
            ->assertStatus(422); // verifica risposta errore validazione  

    }

    // ACTIVE   
    public function test_store_active_boolean_and_nullable()
    {
        $this->postJson(route('company.store'), [
            'businessName' => $this->company->businessName,
            'active' => '',
            'vat' => $this->company->vat,
            'taxCode' => $this->company->taxCode,
            'type' => $this->company->type
        ])
            ->assertJsonStructure(['data'])
            ->assertCreated() //verifica risposta inserimento 201
            ->json();

        $this->postJson(route('company.store'), [
            'businessName' => $this->company->businessName,
            'active' => 'abc',
            'vat' => $this->company->vat,
            'taxCode' => $this->company->taxCode,
            'type' => $this->company->type
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(1, ['errors'])
            ->assertStatus(422); // verifica risposta errore validazione        
    }

    public function test_update_active_boolean()
    {
        $this->patchJson(route('company.update', $this->company->id), [
            'active' => 123
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(1, ['errors'])
            ->assertStatus(422); // verifica risposta errore validazione         
    }

    // BUSINESSNAME
    public function test_store_businessname_string_reuired()
    {
        $this->postJson(route('company.store'), [
            'businessName' => '',
            'vat' => $this->company->vat,
            'taxCode' => $this->company->taxCode,
            'type' => $this->company->type
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(1, ['errors'])
            ->assertStatus(422); // verifica risposta errore validazione  

        $this->postJson(route('company.store'), [
            'businessName' => 123,
            'vat' => $this->company->vat,
            'taxCode' => $this->company->taxCode,
            'type' => $this->company->type
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(1, ['errors'])
            ->assertStatus(422); // verifica risposta errore validazione  

    }

    public function test_update_businessname_string()
    {
        $this->patchJson(route('company.update', $this->company->id), [
            'businessName' => 111
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(1, ['errors'])
            ->assertStatus(422); // verifica risposta errore validazione     
    }

    // VAT
    public function test_store_vat_string_required_11_digits()
    {
        $this->postJson(route('company.store'), [
            'businessName' => $this->company->businessName,
            'vat' => '',
            'taxCode' => $this->company->taxCode,
            'type' => $this->company->type
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(1, ['errors'])
            ->assertStatus(422); // verifica risposta errore validazione  

        $this->postJson(route('company.store'), [
            'businessName' => $this->company->businessName,
            'vat' => 12345678901,
            'taxCode' => $this->company->taxCode,
            'type' => $this->company->type
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(1, ['errors'])
            ->assertStatus(422); // verifica risposta errore validazione        

        $this->postJson(route('company.store'), [
            'businessName' => $this->company->businessName,
            'vat' => '123456789123456',
            'taxCode' => $this->company->taxCode,
            'type' => $this->company->type
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(1, ['errors'])
            ->assertStatus(422); // verifica risposta errore validazione  

        $this->postJson(route('company.store'), [
            'businessName' => $this->company->businessName,
            'vat' => '1234567891p',
            'taxCode' => $this->company->taxCode,
            'type' => $this->company->type
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(1, ['errors'])
            ->assertStatus(422); // verifica risposta errore validazione        
    }

    public function test_update_vat_string_11_digits()
    {
        $this->patchJson(route('company.update', $this->company->id), [
            'vat' => 10123456789
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(1, ['errors'])
            ->assertStatus(422); // verifica risposta errore validazione 

        $this->patchJson(route('company.update', $this->company->id), [
            'vat' => '12345678912345'
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(1, ['errors'])
            ->assertStatus(422); // verifica risposta errore validazione  

        $this->patchJson(route('company.update', $this->company->id), [
            'vat' => '1234567890a'
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(1, ['errors'])
            ->assertStatus(422); // verifica risposta errore validazione       
    }

    // TYPE
    public function test_store_type_required_in_range_1_4()
    {
        $this->postJson(route('company.store'), [
            'businessName' => $this->company->businessName,
            'vat' => $this->company->vat,
            'taxCode' => $this->company->taxCode,
            'type' => ''
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(1, ['errors'])
            ->assertStatus(422); // verifica risposta errore validazione  

        $this->postJson(route('company.store'), [
            'businessName' => $this->company->businessName,
            'vat' => $this->company->vat,
            'taxCode' => $this->company->taxCode,
            'type' => 5
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(1, ['errors'])
            ->assertStatus(422); // verifica risposta errore validazione     
    }

    public function test_update_type_in_range_1_4()
    {
        $this->patchJson(route('company.update', $this->company->id), [
            'type' => 5
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(1, ['errors'])
            ->assertStatus(422); // verifica risposta errore validazione   

        $this->patchJson(route('company.update', $this->company->id), [
            'type' => 2,
            'taxCode' => '01234567890'
        ])
            ->assertJsonStructure(['data'])
            ->assertOk(); // verifica risposta ok   
    }

    // TAXCODE CON TYPE=4
    public function test_store_taxcode_required_string_alphanumeric_size_16_for_type_4_selected()
    {
        $this->postJson(route('company.store'), [
            'businessName' => $this->company->businessName,
            'vat' => $this->company->vat,
            'taxCode' => 1234567890123456, // numeri
            'type' => 4
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(1, ['errors'])
            ->assertStatus(422); // verifica risposta errore validazione  

        $this->postJson(route('company.store'), [
            'businessName' => $this->company->businessName,
            'vat' => $this->company->vat,
            'taxCode' => '!!!!@@@@----....', // caratteri speciali
            'type' => 4
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(1, ['errors'])
            ->assertStatus(422); // verifica risposta errore validazione       

        $this->postJson(route('company.store'), [
            'businessName' => $this->company->businessName,
            'vat' => $this->company->vat,
            'taxCode' => '123456789zxcvbnml',  // 17 caratteri
            'type' => 4
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(1, ['errors'])
            ->assertStatus(422); // verifica risposta errore validazione              

        $this->postJson(route('company.store'), [
            'businessName' => $this->company->businessName,
            'vat' => $this->company->vat,
            'taxCode' => '12345678901asdfg',  // 16 caratteri
            'type' => 4
        ])
            ->assertJsonStructure(['data'])
            ->assertCreated(); // verifica risposta ok      
    }

    public function test_update_taxcode_string_alphanumeric_size_16_for_type_4_selected()
    {
        $this->patchJson(route('company.update', $this->company->id), [
            'taxCode' => '!!!!@@@@----....', // caratteri speciali
            'type' => 4
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(1, ['errors'])
            ->assertStatus(422); // verifica risposta errore validazione 

        $this->patchJson(route('company.update', $this->company->id), [
            'taxCode' => 1234567890123456, //numeri
            'type' => 4
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(1, ['errors'])
            ->assertStatus(422); // verifica risposta errore validazione

        $this->patchJson(route('company.update', $this->company->id), [
            'taxCode' => '12345678zxcvbnml', // 16 caratteri
            'type' => 4
        ])
            ->assertJsonStructure(['data'])
            ->assertOk(); // verifica risposta ok    
    }


    // TAXCODE CON TYPE=[1,2,3]    
    public function test_store_taxcode_numeric_and_size_11_for_type_less_then_4_selected()
    {
        $this->postJson(route('company.store'), [
            'businessName' => $this->company->businessName,
            'vat' => $this->company->vat,
            'taxCode' => '123456789zx',  // 12 caratteri
            'type' => 2
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(1, ['errors'])
            ->assertStatus(422); // verifica risposta errore validazione       

        $this->postJson(route('company.store'), [
            'businessName' => $this->company->businessName,
            'vat' => $this->company->vat,
            'taxCode' => '12345678901',  //11 numeri
            'type' => 2
        ])
            ->assertJsonStructure(['data'])
            ->assertCreated(); // verifica risposta ok        
    }

    public function test_update_taxcode_string_numeric_size_11_for_type_less_then_4_selected()
    {
        $this->patchJson(route('company.update', $this->company->id), [
            'taxCode' => 12345678901, // numeri
            'type' => 2
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(1, ['errors'])
            ->assertStatus(422); // verifica risposta errore validazione 

        $this->patchJson(route('company.update', $this->company->id), [
            'taxCode' => '1234567890123456', // 16 numeri
            'type' => 2
        ])
            ->assertJsonStructure(['errors'])
            ->assertJsonCount(1, ['errors'])
            ->assertStatus(422); // verifica risposta errore validazione

        $this->patchJson(route('company.update', $this->company->id), [
            'taxCode' => '12345678910', // 11 numeri in stringa
            'type' => 2
        ])
            ->assertJsonStructure(['data'])
            ->assertOk(); // verifica risposta ok    
    }
}
