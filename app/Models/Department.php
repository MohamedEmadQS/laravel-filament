<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name', 'deleted_at'
    ];

    public function employees(){
        return $this->hasMany(Employee::class);
    }

    public function team(){
        return $this->belongsTo(Team::class);
    }
}
