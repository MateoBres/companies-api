<?php

namespace App\Traits;

use Illuminate\Validation\Rule;


trait RulesTrait
{
    public function getRules($request)
    {
        isset($request['type'])?$type=$request['type']:$type='';
        $rules = [
            'address' => 'string|nullable',
            'employees' => 'numeric|nullable',
            'active' => 'boolean|nullable',
            'businessName' => 'required|string',
            'vat' => 'required|string|digits:11',
            'type' => ['required', Rule::in([1, 2, 3, 4])],
            'taxCode' => 'required|string|legthForType:'.$type.'|typeForType:'.$type
        ];
        
        return $rules;
    }
}