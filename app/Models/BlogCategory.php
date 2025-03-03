<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BlogCategory extends Model
{
    //
    use HasFactory;
    
    protected $table = 'blog_category';
    
    protected $fillable = [
        'slug',
        'name',
        'icon',
        'description',
    ];
    
     public function blogs()
    {
        return $this->belongsToMany(Blog::class, 'blog_relation', 'category_id', 'blog_id');
    }
}
