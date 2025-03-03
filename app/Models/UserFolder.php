<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFolder extends Model
{
    //table name
    protected $table = 'user_folders';

    // Specify the fields that are mass assignable
    protected $fillable = [
        'user_id',
        'folder_name',
        'access_type',
        'shareable_link',
        'order_number',
    ];
    
    
    /**
     * Relationship with the User model.
     * Each user folder belongs to one user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    
    /**
     * Relationship with the UserTool model.
     * Each user folder belongs to many tool.
     */
    public function tools()
    {
        return $this->hasMany(UserTool::class, 'folder_id');
    }
    
    public function ai_tools()
    {
        return $this->hasManyThrough(AiTool::class, UserTool::class, 'folder_id', 'id', 'id', 'tool_id');
    }


}
