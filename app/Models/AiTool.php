<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AiTool extends Model
{
    //
    use HasFactory;
    
    protected $table = 'ai_tools';
    
    protected $fillable = [
        'slug',
        'name',
        'logo',
        'cover',
        'tagline',
        'short_description_heading',
        'short_description',
        'release_date',
        'verified_status',
        'payment_status',
        'payment_text',
        'platform_compatibility',
        'website_link',
        'description_heading',
        'description',
        'key_features',
        'pros',
        'cons',
        'voila_description',
        'long_description',
        'aitool_filter',
        'integration_capabilities',
        'added_by',
        'avg_rating',
        'reviews_received',
    ];

    protected $casts = [
        'verified_status' => 'integer',
        'payment_status' => 'integer',
        'integration_capabilities' => 'integer',
        'platform_compatibility' => 'string',
    ];

    // Get single verified status text
    public function getVerifiedStatusTextAttribute()
    {
        return self::getVerifiedStatusOptions()[$this->verified_status] ?? 'Unknown';
    }

    // Get single payment status text
    public function getPaymentStatusTextAttribute()
    {
        return self::getPaymentStatusOptions()[$this->payment_status] ?? 'Unknown';
    }
    
    // Get single integration capability status text
    public function getIntegrationCapabilitiesTextAttribute()
    {
        return self::getIntegrationCapabilitiesOptions()[$this->integration_capabilities] ?? 'Unknown';
    }
    
    // Get single platform compatibility text
    public function getPlatformCompatibilityTextAttribute()
    {
        // Get the available platform options
        $platforms = self::getPlatformCompatibilityOptions();

        // Convert stored '1,2,3' into an array
        $ids = explode(',', $this->platform_compatibility);

        // Map the IDs to their names
        return implode('/', array_map(fn($id) => $platforms[$id] ?? 'Unknown', $ids));
        
        //return self::getPlatformCompatibilityOptions()[$this->platform_compatibility] ?? 'Unknown';
    }

    // Accessor for verified status
    public static function getVerifiedStatusOptions()
    {
        return [
            0 => 'Not Verified',
            1 => 'Verified',
        ];
    }

    // Accessor for payment status
    public static function getPaymentStatusOptions()
    {
        return [
            1 => 'Freemium',
            2 => 'Premium',
        ];
    }
    
    // Accessor for integration_capabilities status
    public static function getIntegrationCapabilitiesOptions()
    {
        return [
            0 => 'No',
            1 => 'Yes',
        ];
    }
    
    // Accessor for platform compatibility
    public static function getPlatformCompatibilityOptions()
    {
        return [
            1 => 'Web',
            2 => 'Mobile',
            3 => 'Desktop',
        ];
    }
    
    // Define the relationship between AiTool and AIToolsCategory
    public function category()
    {
        return $this->belongsToMany(AiToolsCategory::class, 'ai_tools_relation', 'ai_tool_id', 'category_id');
    }
    
    public function comments()
    {
        return $this->hasMany(Comment::class, 'tool_id')->whereNull('parent_id')->where('approved', 1)->with('replies')->latest();
    }
}
