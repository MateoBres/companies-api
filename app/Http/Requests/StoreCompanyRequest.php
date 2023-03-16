<?php

namespace App\Http\Requests;

use App\Enums\CompanyTypes;
use App\Rules\ValidateTaxCode;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
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
            'address' => ['string', 'nullable'],
            'employees' => ['numeric', 'nullable'],
            'active' => ['boolean', 'nullable'],
            'businessName' => ['required', 'string'],
            'vat' => ['required', 'string', 'digits:11'],
            'type' => ['required', new Enum(CompanyTypes::class)],
            'taxCode' => ['required', 'string', $type === CompanyTypes::Freelance ? 'alphanum' : 'numeric', new ValidateTaxCode($type)]
        ];
    }
}
