<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
