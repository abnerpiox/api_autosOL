<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $guarded = [];

    public function seller(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sale(){
        return $this->hasOne(Sale::class);
    }
}
