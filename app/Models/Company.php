<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['businessName', 'address', 'vat', 'taxCode', 'employees', 'active', 'type'];

    protected $cast = [
        'type' => CommpanyTypes::class
    ];
}
