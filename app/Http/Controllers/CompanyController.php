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

        // se non viene aggiornato il type e viene aggiornato in taxCode gli passo il type salvarto a DB per la validazione
        if (!$request->get('type') && $request->get('taxCode'))
            $request->request->add(['type' => $company->type]);

        // se viene aggiornato il type e no viene aggiornato in taxCode verifico che il taxCode a DB sia coerente
        if ($request->get('type') && !$request->get('taxCode'))
            $request->request->add(['taxCode' => $company->taxCode]);

        // prendo tutte le regole di validazione e le filtro in base ai campi dell'update
        $rules = $this->getRules($request);
        $arrayRules = [];

        // filtro i campi con non sono stati modificati
        $requestAll = array_filter($request->all(), function ($key) use ($request, $company) {
            return $request->all()[$key] != $company[$key];
        }, ARRAY_FILTER_USE_KEY);

        // prendo le regole che mi servono per la validazione
        foreach (array_keys($requestAll) as $key) {
            $arrayRules[$key] = $rules[$key];
        }

        // se tuttti i valori passati sono identici e quindi non ho nulla da validare lancio un errore
        if (count($arrayRules) == 0) {
            return response()->json([
                'errors' => 'At least one field must be modified'
            ], 200);
        }
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
