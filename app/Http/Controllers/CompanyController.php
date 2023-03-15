<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Dto\CompanyPayload;
use Illuminate\Http\Request;
use App\Services\CompanyService;
use App\Http\Resources\CompanyResource;
use App\Http\Requests\StoreCompanyRequest;

class CompanyController extends Controller
{  
    public function __construct( private readonly CompanyService $companyService ) 
    {
    } 

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
        // $company = Company::create($request->validated());
        $company = $this->companyService->createCompany(
            CompanyPayload::newInstanceFrom($request->validated())
        );

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


    public function update(Company $company, Request $request)
    {
        // $company = Company::findOrFail($id);
        
        // // filtro i campi che non sono stati modificati
        // $requestAll = array_filter($request->all(), function ($key) use ($request, $company) {
        //     return $request->all()[$key] != $company[$key];
        // }, ARRAY_FILTER_USE_KEY);
      
        // // se tuttti i valori passati sono identici a quelli attuali lancio un errore
        // if (count($requestAll) == 0) {
        //     return response()->json([
        //         'errors' => 'At least one field must be modified'
        //     ], 200);
        // }

        // se non viene aggiornato il type e viene aggiornato in taxCode gli passo il type preso a DB per la validazione e viceversa
        
        // TODO questa parte viene esguita nel ValidateTaxcodeRule
        // if (isset($request['taxCode']) && !isset($request['type'])){
        //     $request['type'] = $company->type;
        //     $request->request->add(['type' => $company->type]);
        // }

        // if (isset($request['type']) && !isset($request['taxCode'])){
        //     $request['taxCode'] = $company->taxCode;
        //     $request->request->add(['taxCode' => $company->taxCode]);
        // }

        // $rules = $this->getRules($request);
        
        //filtro le rules in base ai campi ricevuto nella request
        // $rules = array_intersect_key($rules, $request);
        // $request->validate($rules);

        $company = $this->companyService->updateCompany(
            $company, CompanyPayload::newInstanceFrom($request->validatedOrDefault($company))
        );

        // $company->update($request->all());

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
?>