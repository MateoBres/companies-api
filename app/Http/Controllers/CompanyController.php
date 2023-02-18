<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use stdClass;

class CompanyController extends Controller
{
    //classe con le regole di validazione in base al type per store e update
    use Traits\Rules;


    public function index(Request $request)
    {
        $query = Company::query();
        $perPage = 15;
        $page = $request->input('page', 1);

        $result = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

        //creo l'ogggetto pagination customizzato
        $meta = new stdClass;
        $meta->page = $page;
        $meta->perPage = $perPage;
        $meta->total = count(Company::all());

        return response()->json([            
            'data' => $result,
            'meta' => $meta
        ], 200);
    }


    public function store(Request $request)
    {
        //verifico se type == null o fuori range evito l'invio l'errore sulla lunghezza del taxCode valutando la sua lunghezza
        //type 4 significa che permetto la registrazione con codice fiscale (16 caratteri, lettere e numeri) altrimenti PI (11 caratteri solo numeri)
        if ((!$request->get('type') || !in_array($request->get('type'),[1,2,3,4])) && $request->get('taxCode') !== null) {
            if (strlen($request->get('taxCode')) == 16) {
                $request->validate($this->getRules()['rulesStore1']); //metodo nel trait/Rules
            } else {
                $request->validate($this->getRules()['rulesStore2']); //metodo nel trait/Rules
            }
        }
        if ($request->get('type') == 4) {
            $request->validate($this->getRules()['rulesStore1']); //metodo nel trait/Rules
        } else {
            $request->validate($this->getRules()['rulesStore2']); //metodo nel trait/Rules
        }

        $company = Company::create($request->all());

        return response()->json([            
            'data' => $company
        ], 201);
    }


    public function show($id, Request $request)
    {
        $company = Company::find($id);

        // mostro un errore in caso non esista l'id cercato dall'utente
        if (!$company) {
            return response()->json([
                'errors' => 'Data not found'
            ], 422);
        } else {
            $company->update($request->all());

            return response()->json([                
                'data' => $company
            ], 200);
        }
    }


    public function update($id, Request $request)
    {
        // se non ricevo neanche un campo lancio l'errore
        if (count($request->all()) == 0 || $request->get('businessName') == null && $request->get('address') == null && $request->get('vat') == null && $request->get('taxCode') == null && $request->get('employees') == null && $request->get('active') == null && $request->get('type') == null) {
            return response()->json([
                'errors' => 'At lest one filed must be modified'
            ], 422);
        };

        $company = Company::find($id);

        // mostro un errore in caso non esista l'id cercato dall'utente
        if (!$company) {
            return response()->json([
                'errors' => 'Data not found'
            ], 422);
        } else {
            //type 4 significa che permetto la registrazione con codice fiscale (16 caratteri, lettere e numeri) altrimenti PI (11 caratteri solo numeri)
            if ($request->get('type') == 4) {
                $request->validate($this->getRules()['rulesUpdate1']); //metodo nel trait/Rules
            } else {
                $request->validate($this->getRules()['rulesUpdate2']); //metodo nel trait/Rules
            }

            $company->update($request->all());

            return response()->json([                
                'data' => $company
            ], 200);
        }
    }


    public function destroy($id)
    {
        $company = Company::find($id);

        // mostro un errore in caso non esista l'id cercato dall'utente
        if (!$company) {
            return response()->json([
                'errors' => 'Data not found'
            ], 422);
        } else {
            $company->delete();

            return response()->noContent();
        }
    }
}