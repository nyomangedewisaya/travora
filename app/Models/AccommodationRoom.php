<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccommodationRoom extends Model
{
    protected $table = 'accommodation_rooms';
    protected $guarded = ['id'];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function accommodation() {
        return $this->belongsTo(Accommodation::class);
    }
}
