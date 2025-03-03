<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentAnswers extends Model
{
    //table name
    protected $table = 'comment_answers';

    // Specify the fields that are mass assignable
    protected $fillable = [
        'user_id',
        'tool_id',
        'comment_id',
        'comment',
        'approved',
        'like_count',
        'dislike_count',
    ];
    
    public function question()
    {
        return $this->belongsTo(CommentQuestions::class, 'comment_id', 'id');
    }
    
    public function likeDislike()
    {
        return $this->belongsTo(CommentsLikeDislike::class, 'comment_answer_id', 'id');
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
}
