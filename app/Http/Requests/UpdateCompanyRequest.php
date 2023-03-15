<?php

namespace App\Http\Requests;

use App\Models\Company;
use App\Enums\CompanyTypes;
use App\Rules\ValidateTaxCode;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $type = CompanyTypes::from($this->input('type'));

        return [
            'address' => 'string|fillable',
            'employees' => 'numeric|fillable',
            'active' => 'boolean|fillable',
            'businessName' => 'required|fillable|string',
            'vat' => 'required|string|fillable|digits:11',
            'type' => ['required', 'fillable', new Enum(CompanyTypes::class)],
            'taxCode' => ['required', 'fillable', 'string', $type === CompanyTypes::Freelance ? 'alphanum' : 'numeric', new ValidateTaxCode($type)]
        ];
    }

    public function validatedOrDefault(Company $company)
    {
        $result = [];
        $fillable = $company->getFillable();
        foreach($fillable as $field)
        {
            $result[$field] = $this->validated($field, $company->{$field}); 
        }

        return $result;
    }
}
