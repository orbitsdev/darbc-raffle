<?php

namespace App\Models;

use App\Models\Prize;
use App\Models\Member;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Winner extends Model
{
    use HasFactory;

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * A winner is assigned a prize.
     */
    public function prize()
    {
        return $this->belongsTo(Prize::class);
    }
}
