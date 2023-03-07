<?php

namespace App\Dto;

use App\Enums\CompanyTypes;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class CompanyPayload implements Arrayable
{
    public function __construct(
        public string $businessName,
        public string $vat,
        public string $taxCode,
        public ?int $employees,
        public ?bool $active,
        public CompanyTypes $type,
        public ?string $address,
    ) {
    }

    public static function newInstanceFrom(array $data)
    {
        return new self(
            businessName: $data['businessName'],
            vat: $data['vat'],
            taxCode: $data['taxCode'],
            employees: Arr::get($data, 'employees', 0),
            active: Arr::get($data, 'active', false),
            type: CompanyTypes::from(Arr::get($data, 'type')),
            address: Arr::get($data, 'address'),
        );
    }

    public function toArray(): array
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
