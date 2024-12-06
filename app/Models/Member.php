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
        $lastName = $this->last_name ?? '';
        $firstName = $this->first_name ?? '';
        $middleName = $this->middle_name ?? '';
    
        // Format: LastName, FirstName MiddleName
        $fullName = trim($lastName);
    
        if (!empty($firstName)) {
            $fullName .= ', ' . trim($firstName);
        }
    
        if (!empty($middleName)) {
            $fullName .= ' ' . trim($middleName);
        }
    
        return $fullName;
    }
    


public function getImage()
{



    if ($this->hasMedia('image')) {
        return $this->getFirstMediaUrl('image');
    }

    return asset('images/placeholder-image.jpg');
}

}
