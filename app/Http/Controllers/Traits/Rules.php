<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Validation\Rule;

trait Rules
{

    public $rulesMaster;
    public $rulesStore;
    public $rulesStore1;
    public $rulesStore2;
    public $rulesUpdate;
    public $rulesUpdate1;
    public $rulesUpdate2;


    public function getRules()
    {
        // lista master delle rules non reuired
        $this->rulesMaster = [
            'address' => 'string|nullable',
            'employees' => 'numeric|nullable',
            'active' => 'boolean|nullable'
        ];

        // regole base per lo store
        $this->rulesStore = [
            'businessName' => 'required|string',
            'vat' => 'required|string|digits:11',
            'type' => ['required', Rule::in([1, 2, 3, 4])]
        ];


        //regole base per l'update
        $this->rulesUpdate = [
            'businessName' => 'string',
            'vat' => 'string|digits:11',
            'type' => Rule::in([1, 2, 3, 4])
        ];

        //2 possibili variazioni di regole di validazione per il metodo STORE in base al type
        $this->rulesStore1 = $this->rulesMaster + $this->rulesStore + ['taxCode' => 'required|string|alpha_num|size:16']; //permetto tutti i caratteri perchè mi aspetto una C.F.
        $this->rulesStore2 = $this->rulesMaster + $this->rulesStore + ['taxCode' => 'required|string|digits:11']; //permetto solo numeri perchè mi aspetto una P.I.

        //2 possibili variazioni di regole di validazione per il metodo UPDATE in base al type
        $this->rulesUpdate1 = $this->rulesMaster + $this->rulesUpdate + ['taxCode' => 'string|alpha_num|size:16']; //permetto tutti i caratteri perchè mi aspetto una C.F.
        $this->rulesUpdate2 = $this->rulesMaster + $this->rulesUpdate + ['taxCode' => 'string|numeric|digits:11']; //permetto solo numeri perchè mi aspetto una P.I.

        //preparao l'array con tutte le 4 possibili regole
        $rules = [
            'rulesStore1' => $this->rulesStore1,
            'rulesStore2' => $this->rulesStore2,
            'rulesUpdate1' => $this->rulesUpdate1,
            'rulesUpdate2' => $this->rulesUpdate2
        ];

        return $rules;
    }
}