<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Social_Media_Account extends Model
{
    protected $fillable = ['social_media_providers_id', 'account_name', 'profile_url'];
    public function getClientSocialMediaAccounts()
    {
        return $this->belongsTo(Client::class, 'clients_id', 'id');
    }

    public function provider()
    {
        return $this->belongsTo(Social_Media_Provider::class, 'social_media_providers_id');
    }
}
