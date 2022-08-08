<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dailysale extends Model
{
    use HasFactory;

    protected $fillable =[
                    'amount','date'
    ];
}
