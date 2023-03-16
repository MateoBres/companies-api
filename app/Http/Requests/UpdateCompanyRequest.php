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
            // i campi fillable posso on esserci quindi controllo se c'e' il campo altrimenti gli passo il campo della company da db
            $result[$field] = $this->validated($field, $company->{$field}); 
        }

        return $result;
    }
}
