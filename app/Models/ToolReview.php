<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToolReview extends Model
{
    //table name
    protected $table = 'tool_reviews';

    // Specify the fields that are mass assignable
    protected $fillable = [
        'tool_id',
        'user_id',
        'rating',
        'review',
        'approved',
    ];
    
    
    /**
     * Relationship with the AiTool model.
     * Each review belongs to one tool.
     */
    public function tool()
    {
        return $this->belongsTo(AiTool::class);
    }

    /**
     * Relationship with the User model.
     * Each review belongs to one user.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
