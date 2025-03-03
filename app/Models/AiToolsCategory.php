<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AiToolsCategory extends Model
{
    //
    use HasFactory;
    
    protected $table = 'ai_tool_category';
    
    protected $fillable = [
        'parent_category_id',
        'slug',
        'name',
        'icon',
        'description',
    ];
    
     public function parent()
    {
        return $this->belongsTo(AiToolsCategory::class, 'parent_category_id');
    }

    public function children()
    {
        return $this->hasMany(AiToolsCategory::class, 'parent_category_id');
    }
    
    public function tools()
    {
        return $this->belongsToMany(AiTool::class, 'ai_tools_relation', 'category_id', 'ai_tool_id');
    }
}
