<?php

namespace App\Models;

use App\Models\OrderBookSpot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoinPair extends Model
{
    use HasFactory;
    public function crypto():hasOne    
    {
        return $this->belongsTo(CryptoCurrency::class, 'crypto_currency_id');
    }

    public function currency():hasOne
    {
        return $this->belongsTo(CryptoCurrency::class, 'crypto_currency_id');
    }
    public function crypto1():hasOne    
    {
        return $this->belongsTo(CryptoCurrency::class, 'CryptoCurrency_id1');
    }

    public function currency1():hasOne
    {
        return $this->belongsTo(CryptoCurrency::class, 'CryptoCurrency_id1');
    }
    /**
     * Get all of the OrderBookSpot for the CoinPair
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function OrderBookSpot(): HasMany
    {
        return $this->hasMany(OrderBookSpot::class, 'foreign_key', 'id');
    }
}?>
