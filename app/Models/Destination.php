<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    protected $table = 'destinations';
    protected $guarded = ['id'];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    public function accommodations()
    {
        return $this->hasMany(Accommodation::class);
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function parent()
    {
        return $this->belongsTo(Destination::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Destination::class, 'parent_id');
    }
}
