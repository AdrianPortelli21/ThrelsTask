<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'eventname',
        'start',
        'end',
        'user_id'
    ];




    public function user(){
        return $this->belongsto(User::class);
    }
}
