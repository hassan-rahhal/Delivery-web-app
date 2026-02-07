<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMediaAccount extends Model
{
    use HasFactory;
    // App\Models\SocialMediaAccount.php
    protected $fillable = [
        'social_media_providers_id',
        'account_name',
        'profile_url',
    ];

    protected $table = 'social__media__accounts'; // match your actual table name
     public function provider()
    {
        return $this->belongsTo(Social_Media_Provider::class, 'social_media_providers_id');
    }
}
