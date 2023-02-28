<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Company
 */
class CompanyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'businessName' => $this->businessName,
            'address' => $this->address,
            'vat' => $this->vat,
            'taxCode' => $this->taxCode,
            'employees' => $this->employees,
            'active' => $this->active,
            'type' => $this->type,
        ];
    }
}
