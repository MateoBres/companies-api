<?php

namespace App\Rules;

use App\Enums\CompanyTypes;
use Illuminate\Contracts\Validation\Rule;

class ValidateTaxCode implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(
        private CompanyTypes $companyType
    ) {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return match ($this->companyType) {
            CompanyTypes::SRL, CompanyTypes::SPA, CompanyTypes::SNC => $this->validateCompanyTaxCode($value),
            CompanyTypes::Freelance => $this->validateFreelanceTaxCode($value),
            default => false
        };
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }

    private function validateCompanyTaxCode(string $taxCode)
    {
        return match (strlen($taxCode)) {
            11 => 'digits:11',
            default => true
        };
    }
    
    private function validateFreelanceTaxCode(string $taxCode)
    {
        return match (strlen($taxCode)) {
            16 => 'size:16',
            default => true
        };
    }
}
