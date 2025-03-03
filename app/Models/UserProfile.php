<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserProfile extends Model
{
    //
    use HasFactory;

    protected $table = 'user_profile';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone_num',
        'industry',
        'ai_expertise_level',
        'area_of_interest',
        'view_platform'
    ];
    
    public function user() 
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
