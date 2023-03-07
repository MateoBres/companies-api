<?php

namespace App\Http\Requests;

use App\Enums\CompanyTypes;
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
        // dd($this->get('taxCode'));
        $this->get('type')!=null?$type=$this->get('type'):$type='';
        $rules = [
            'address' => ['string','nullable'],
            'employees' => ['numeric','nullable'],
            'active' => ['boolean','nullable'],
            'businessName' => ['required','string'],
            'vat' => ['required','string','digits:11'],
            // 'type' => ['required', Rule::in([1, 2, 3, 4])],
            'type' => ['required', new Enum(CompanyTypes::class)],
            // 'taxCode' => ['required|string|legthForType:'.$type.'|typeForType:'.$type
            // 'taxCode' => ['required', 'string', $type === 4 ? 'digits' : 'alphanum', new ValidateTaxCode($type)]
            // 'taxCode' => ['required', 'string', $type === 4 ? 'alphanum' : 'integer', 'legthForType:'.$type]
            'taxCode' => ['required', 'string', 'typeForType:'.$type, 'legthForType:'.$type]//ho bisogno del metodo typeForType per validare solo quando c'e' un type valido
        ];
        
        return $rules;
    }
}
