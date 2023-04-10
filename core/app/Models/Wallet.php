<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function crypto()
    {
        return $this->belongsTo(CryptoCurrency::class, 'crypto_currency_id');
    }
    
    public function currency()
    {
        return $this->belongsTo(CryptoCurrency::class,'crypto_currency_id');
    }

    public function scopeHasCurrency(){
        return $this->whereHas('currency',function($query){
            $query->where('status',1);
        });
    }

    public function scopeCheckWallet($filter,$data){
        return $this->hasCurrency()->where('user_id',$data['user']->id)->with('currency');
    }
}
