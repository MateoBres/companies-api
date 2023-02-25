<?php

namespace App\Http\Controllers;

use stdClass;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{

    public function getRules($request)
    {
        $rules = [
            'address' => 'string|nullable',
            'employees' => 'numeric|nullable',
            'active' => 'boolean|nullable',
            'businessName' => 'required|string',
            'vat' => 'required|string|digits:11',
            'type' => ['required', Rule::in([1, 2, 3, 4])],
            'taxCode' => 'required|string|legthForType:'.$request['type'].'|typeForType:'.$request['type']
            // 'taxCode' => (isset($request['type'])? (!in_array($request['type'], [1, 2, 3, 4]) ? '' : ($request['type'] == 4 ? 'required|string|alpha_num|size:16' : 'required|string|digits:11')):'')
        ];
        
        return $rules;
    }


    public function index()
    {        
        $company = Company::paginate()->toArray(); // paginate modificato in vendor/laravel/framework/src/Illuminate/Pagination/LengthAwarePaginator

        return response()->json([
           'data' => $company['data'],
           'meta' => $company['meta'] 
        ], 200);
    }


    public function store(Request $request)
    {
        $request->validate($this->getRules($request->all()));

        $company = Company::create($request->all());

        return response()->json([
            'data' => $company
        ], 201);
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
