<?php

namespace App\Models;

use App\Models\Prize;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $casts = [
        'is_active' => 'boolean',
    ];
    public function prizes()
{
    return $this->hasMany(Prize::class);
}


public function scopeIsActive($query){
    return $query->where('is_active',true);
}

public function scopeHasPrizes($query)
    {
        return $query->whereHas('prizes', function ($prizeQuery) {
            $prizeQuery->where('quantity', '>', 0); // Only include prizes with a quantity greater than 0
        });
    }
}
