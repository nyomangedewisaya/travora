<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'reviews';
    protected $guarded = ['id'];

    public function customer() {
        return $this->belongsTo(User::class);
    }

    public function reviewable() {
        return $this->morphTo();
    }
}
