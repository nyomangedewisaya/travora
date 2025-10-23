<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accommodation extends Model
{
    protected $table = 'accommodations';
    protected $guarded = ['id'];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function partner() {
        return $this->belongsTo(User::class);
    }

    public function destination() {
        return $this->belongsTo(Destination::class);
    }

    public function reviews() {
        return $this->morphMany(Review::class, 'reviewble');
    }

    public function media() {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function rooms() {
        return $this->hasMany(AccommodationRoom::class);
    }
}
