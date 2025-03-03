<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommunityAnswers extends Model
{
    //table name
    protected $table = 'community_answers';

    // Specify the fields that are mass assignable
    protected $fillable = [
        'user_id',
        'category_id',
        'community_question_id',
        'parent_answer_id',
        'answer',
        'approved',
        'like_count',
        'dislike_count',
    ];
    
    public function question()
    {
        return $this->belongsTo(CommunityQuestions::class, 'community_question_id', 'id');
    }
    
    public function likeDislike()
    {
        return $this->hasMany(CommunityLikeDislike::class, 'community_answer_id', 'id');
    }
    
    public function childAnswers() {
        return $this->hasMany(CommunityAnswers::class, 'parent_answer_id', 'id')->where('approved', 1)->with( 'childAnswers' );
    }

    /**
     * Relationship with User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Relationship with AI Tool Category
     */
    public function aiToolCategory(): BelongsTo
    {
        return $this->belongsTo(AiToolsCategory::class, 'category_id', 'id');
    }
}
