<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryCourse extends Model
{
    use HasFactory;
    
    protected $table = 'course_category';


    protected $fillable = ['name' , 'icon' , 'slug' , 'description'];

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_relation', 'category_id', 'course_id');
    }


}
