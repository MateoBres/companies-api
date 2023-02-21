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
            'taxCode' => (!in_array($request->get('type'), [1, 2, 3, 4]) ? '' : ($request->get('type') == 4 ? 'required|string|alpha_num|size:16' : 'required|string|digits:11'))
        ];

        return $rules;
    }


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
        $request->validate($this->getRules($request));

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

        // creo un array con le regole di validazione che mi servono in base ai campi dell'update
        $rules = $this->getRules($request);
        
        $arrayRules = $requestAll;

        array_walk($arrayRules, function (&$value, $key) use ($rules) {
            $value = $rules[$key];
        });

        $request->validate($arrayRules);

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
