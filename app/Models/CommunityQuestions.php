<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommunityQuestions extends Model
{
    //table name
    protected $table = 'community_questions';

    // Specify the fields that are mass assignable
    protected $fillable = [
        'user_id',
        'category_id',
        'question_title',
        'question_brief',
        'approved',
    ];
    
    public function answer()
    {
        return $this->hasMany(CommunityAnswers::class, 'community_question_id', 'id');
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
