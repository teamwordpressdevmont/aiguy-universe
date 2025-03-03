<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTool extends Model
{
    //table name
    protected $table = 'user_tools';

    // Specify the fields that are mass assignable
    protected $fillable = [
        'folder_id',
        'user_id',
        'tool_id',
    ];
    
    
    /**
     * Relationship with the UserFolder model.
     * Each user tool belongs to one folder.
     */
    public function folder()
    {
        return $this->belongsTo(UserFolder::class, 'folder_id');
    }
    
    /**
     * Relationship with the User model.
     * Each user tool belongs to one user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Relationship with the AiTool model.
     * Each user tool belongs to one Tool.
     */
    public function tool()
    {
        return $this->belongsTo(AiTool::class); // Assuming there's a Tool model
    }
     
}
