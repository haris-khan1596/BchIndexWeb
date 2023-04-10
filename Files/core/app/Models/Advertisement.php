<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    public function fiat()
    {
        return $this->belongsTo(FiatCurrency::class, 'fiat_currency_id');
    }

    public function fiatGateway()
    {
        return $this->belongsTo(FiatGateway::class);
    }

    public function crypto()
    {
        return $this->belongsTo(CryptoCurrency::class, 'crypto_currency_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tradeRequests()
    {
        return $this->hasMany(Trade::class, 'trade_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    public function scopeBuy($query)
    {
        return $query->where('type', 1);
    }

    public function scopeSell($query)
    {
        return $query->where('type', 2);
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(
            get: fn () => $this->statusBadgeData(),
        );
    }

    public function statusBadgeData()
    {
        $html = '';
        if ($this->status) {
            $html = '<span class="badge badge--success">' . trans("Enabled") . '</span>';
        } else {
            $html = '<span class="badge badge--danger">' . trans("Disabled") . '</span>';
        }
        return $html;
    }

    public function typeBadge(): Attribute
    {
        return new Attribute(
            get: fn () => $this->typeBadgeData(),
        );
    }

    public function typeBadgeData()
    {
        $html = '';
        if ($this->type == 1) {
            $html = '<span class="badge badge--primary">' . trans("Buy") . '</span>';
        } else {
            $html = '<span class="badge badge--warning">' . trans("Sell") . '</span>';
        }
        return $html;
    }


    public function marginValue(): Attribute
    {
        return new Attribute(
            get: fn () => $this->showMarginData(),
        );
    }

    protected function showMarginData()
    {
        $html = '';
        if ($this->fixed_price > 0) {
            $html = '<span class="text--warning">' . trans('Fixed') .'</span>';
        } else {
            $html = '<span class="text--info">' . trans('Margin') . ': ' . getAmount($this->margin) . '%</span>';
        }
        return $html;
    }
}
