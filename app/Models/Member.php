<?php

namespace App\Models;

use App\Models\Winner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Member extends Model
{
    use HasFactory;

    public function winners()
    {
        return $this->hasMany(Winner::class);
    }

    public function getFullNameAttribute()
{
    $firstName = $this->first_name ?? '';
    $lastName = $this->last_name ?? '';

    return $firstName . ' ' . $lastName;
}



public function getImage()
{



    if ($this->hasMedia('image')) {
        return $this->getFirstMediaUrl('image');
    }

    return asset('images/placeholder-image.jpg');
}

}
