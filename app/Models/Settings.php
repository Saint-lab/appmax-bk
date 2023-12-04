<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
     protected $table = "user_settings";

     protected $fillable = [
   	    'user_id',
        'google_key',
        'twilio_number',
        'twilio_sid',
        'twilio_token',
        
    ];
}
