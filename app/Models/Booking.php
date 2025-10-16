<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'bookings';
    protected $guarded = ['id'];

    public function customer()
    {
        return $this->belongsTo(User::class);
    }
    
    public function items() {
        return $this->hasMany(BookingItem::class);
    }
}
