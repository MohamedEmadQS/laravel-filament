<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable=[
        'name',
        'slug',
    ];

    public function employees(){
        return $this->hasMany(Employee::class);
    }
    public function members(){
        return $this->belongsToMany(User::class);
    }
    public function users(){
        return $this->belongsToMany(User::class);
    }

    public function states(){
        return $this->hasMany(State::class);
    }

    public function countries(){
        return $this->hasMany(Country::class);
    }

    public function cities(){
        return $this->hasMany(City::class);
    }
}
