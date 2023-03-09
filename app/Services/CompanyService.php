<?php

namespace App\Services;

use App\Dto\CompanyPayload;
use App\Models\Company;

class CompanyService
{
    public function createCompany(CompanyPayload $companyPayload): Company
    {
        $company = new Company();
        $company->fill(
            $companyPayload->toArray()
        );

        // Add relations (belongs to)

        return tap($company, fn (Company $company) => $company->save());
        //return tap($company, function (Company $company) {
        //    $company->save();
        //
        //    // Add relations (save one, save many, many to many...)
        //    // DO SOMETHING ELSE
        //});
    }


    public function updateCompany($company, $request): Company
    {
        

        // Add relations (belongs to)

        return tap($company, fn (Company $company) => $company->update($request->all()));
        //return tap($company, function (Company $company) {
        //    $company->save();
        //
        //    // Add relations (save one, save many, many to many...)
        //    // DO SOMETHING ELSE
        //});
    }
}
?>