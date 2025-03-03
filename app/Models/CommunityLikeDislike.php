<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityLikeDislike extends Model
{
    //table name
    protected $table = 'community_like_dislikes';

    // Specify the fields that are mass assignable
    protected $fillable = [
        'user_id',
        'category_id',
        'question_id',
        'community_answer_id',
        'like',
        'dislike',
    ];
}
