<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;


class User extends Authenticatable
{
    use Notifiable;

    public function roles(){
        return $this->belongsToMany(Role::class);
    }

    public function cars(){
        return $this->hasMany(Car::class);
    }

    public function sales(){
        return $this->hasMany(Sale::class);
    }

    public function generateToken(){
        $this->api_token = Str::random(60);
        $this->save();

        return $this->api_token;
    }

    public function authorizeRoles($roles){
        if($this->hasAnyRole($roles)){
            return true;
        };
        return false;
    }

    protected function hasAnyRole($roles){
        if(is_array($roles)){
            foreach ($roles as $role){
                if($this->hasRole($role)){
                    return true;
                }
            }
        }else{
            if($this->hasRole($roles)){
                return true;
            }
        }

        return false;
    }

    protected function hasRole($role){
        if($this->roles()->where('name', $role)->first()){
            return true;
        }

        return false;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
