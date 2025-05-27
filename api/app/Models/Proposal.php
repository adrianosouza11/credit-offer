<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'cpf',
        'simulateCredit',
        'institutionName',
        'institutionCode',
        'modalityName',
        'modalityCode',
        'modalityMonthInt',
        'totalPaid',
        'instNumber'
    ];
}
