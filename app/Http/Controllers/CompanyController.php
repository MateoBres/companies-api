<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Traits\RulesTrait;
use Illuminate\Http\Request;
use App\Http\Resources\CompanyResource;
use App\Http\Requests\StoreCompanyRequest;

class CompanyController extends Controller
{
    use RulesTrait;
  
    public function index()
    {        
        $company = Company::paginate()->toArray(); // paginate modificato in vendor/laravel/framework/src/Illuminate/Pagination/LengthAwarePaginator

        return response()->json([
           'data' => $company['data'],
           'meta' => $company['meta'] 
        ], 200);
    }


    public function store(StoreCompanyRequest $request)
    {
        $company = Company::create($request->validated());

        return new CompanyResource($company);
    }


    public function show($id, Request $request)
    {
        $company = Company::findOrFail($id);

        $company->update($request->all());

        return response()->json([
            'data' => $company
        ], 200);
    }


    public function update($id, Request $request)
    {
        $company = Company::findOrFail($id);
        
        // filtro i campi che non sono stati modificati
        $requestAll = array_filter($request->all(), function ($key) use ($request, $company) {
            return $request->all()[$key] != $company[$key];
        }, ARRAY_FILTER_USE_KEY);
      
        // se tuttti i valori passati sono identici a quelli attuali lancio un errore
        if (count($requestAll) == 0) {
            return response()->json([
                'errors' => 'At least one field must be modified'
            ], 200);
        }

        // se non viene aggiornato il type e viene aggiornato in taxCode gli passo il type preso a DB per la validazione e viceversa
        if (isset($requestAll['taxCode']) && !isset($requestAll['type'])){
            $requestAll['type'] = $company->type;
            $request->request->add(['type' => $company->type]);
        }

        if (isset($requestAll['type']) && !isset($requestAll['taxCode'])){
            $requestAll['taxCode'] = $company->taxCode;
            $request->request->add(['taxCode' => $company->taxCode]);
        }

        $rules = $this->getRules($requestAll);
        
        //filtro le rules in base ai campi ricevuto nella request
        $rules = array_intersect_key($rules, $requestAll);
        $request->validate($rules);

        $company->update($request->all());

        return response()->json([
            'data' => $company
        ], 200);
    }


    public function destroy($id)
    {
        $company = Company::findOrFail($id);

        $company->delete();

        return response()->noContent();
    }
}
