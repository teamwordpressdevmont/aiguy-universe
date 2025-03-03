<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Blog extends Model
{
    //
    use HasFactory;
    
    protected $table = 'blogs';
    
    protected $fillable = [
        'id',
        'user_id',
        'name',
        'featured_image',
        'slug',
        'reading_time',
        'content',
        'left_image',
        'right_text',
        'middle_text',
        'middle_image',
        'sub_title',
        'sub_content',
        'sub_image',
    ];
    
     // Define the relationship between AiTool and AIToolsCategory
    public function category()
    {
        return $this->belongsToMany(BlogCategory::class, 'blog_relation', 'blog_id', 'category_id');

    }
    
    
    //User Data
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
