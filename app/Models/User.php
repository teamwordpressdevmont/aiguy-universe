<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;



class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable , HasApiTokens, HasRoles;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'password',
        'avatar',
        'google_id',
        'email_verified',
        'google_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    
    // Return the unique identifier for the user
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    // Return an array of custom claims to add to the token
    public function getJWTCustomClaims()
    {
        return [];
    }
    
    public function user_profile() 
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'id');
    }
    
    public function toolReview()
    {
        return $this->hasMany(ToolReview::class, 'user_id', 'id');
    }
}