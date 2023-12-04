<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersPhoneNumber extends Model
{
    use HasFactory;
    protected $table= "users_number";
   protected $fillable = [
   	    'user_id',
        'phone_number',
        'status'
    ];
}
