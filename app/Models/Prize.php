<?php

namespace App\Models;

use App\Models\Event;
use App\Models\Winner;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Prize extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    public function event()
{
    return $this->belongsTo(Event::class);
}

    public function winners()
    {
        return $this->hasMany(Winner::class);
    }
    public function registerMediaCollections(): void
{
    $this->addMediaCollection('image')
            
    ->singleFile();
}
}
