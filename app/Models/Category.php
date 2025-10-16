<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $guarded = ['id'];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function packages() {
        return $this->hasMany(Package::class);
    }

    public function accommodations() {
        return $this->hasMany(Accommodation::class);
    }
}
