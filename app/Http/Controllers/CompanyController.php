<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Dto\CompanyPayload;
use Illuminate\Http\Request;
use App\Services\CompanyService;
use App\Http\Resources\CompanyResource;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;

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


    public function update(Company $company, UpdateCompanyRequest $request)
    {
        $company = $this->companyService->updateCompany(
            // il primo parametro company e' l'oggeto che va modificato
            $company, CompanyPayload::newInstanceFrom($request->validatedOrDefault($company))
        );

        return response()->json([
            'data' => $company
        ], 200);
    }


    public function destroy(Company $company)
    {
        $company->delete();

        return response()->noContent();
    }
}
?>