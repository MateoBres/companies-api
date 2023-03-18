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
            'address' => ['filled', 'string'],
            'employees' => ['filled', 'numeric'],
            'active' => ['filled', 'boolean'],
            'businessName' => ['filled', 'string'],
            'vat' => ['filled', 'string', 'digits:11'],
            'type' => ['filled', new Enum(CompanyTypes::class)],
            'taxCode' => ['filled', 'string', $type === CompanyTypes::Freelance ? 'alphanum' : 'numeric', new ValidateTaxCode($type)]
        ];
    }

    public function validatedOrDefault(Company $company)
    {
        $fillable = $company->getFillable();
        $result = [];
        foreach($fillable as $field)
        {
            // se ho un campo non modificato gli assegno il valore che aveva
            $result[$field] = $this->validated($field, $company->{$field}??$company->first()->getAttributes()[$field]);
        }

        return $result;
    }
}
