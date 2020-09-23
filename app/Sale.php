<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $guarded = [];

    public function buyer(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function car(){
        return $this->belongsTo(Car::class, 'car_id');
    }
}
