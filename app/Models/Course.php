<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    //
    use HasFactory;
    
    protected $table = 'courses';
    
    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'cover',
        'pricing',
        'affiliate_link',
        'members',
        'courses_filter',
    ];
    
    public function categoryCourses()
    {
      return $this->belongsToMany(CategoryCourse::class, 'course_relation', 'course_id', 'category_id');
    }

}
