<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentQuestions extends Model
{
    //table name
    protected $table = 'comment_questions';

    // Specify the fields that are mass assignable
    protected $fillable = [
        'user_id',
        'tool_id',
        'comment_title',
        'comment_content',
        'approved',
    ];
    
    public function answer()
    {
        return $this->hasMany(CommentAnswers::class, 'comment_id', 'id');
    }

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

    public function communityReplies()
    {
        return $this->hasMany(CommunityAnswers::class, 'community_question_id', 'id')->where('approved', 1);
    }
}
