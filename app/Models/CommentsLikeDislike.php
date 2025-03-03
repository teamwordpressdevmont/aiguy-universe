<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentsLikeDislike extends Model
{
    //table name
    protected $table = 'comments_like_dislikes';

    // Specify the fields that are mass assignable
    protected $fillable = [
        'user_id',
        'tool_id',
        'comment_id',
        'comment_answer_id',
        'like',
        'dislike',
    ];
}
