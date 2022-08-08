<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable =[
        'user_id','amount','status'
    ];

    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }

    public function items()
    {
        return $this->hasOne(Item::class,'id','item_id');
    }
}
