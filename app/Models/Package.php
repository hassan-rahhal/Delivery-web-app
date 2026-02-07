<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'picture',
        'height',
        'width',
        'depth',
        'weight',
        'weight_unit',
        'measurement_unit',
        'is_breakable',
        'is_flammable',
        'has_fluid',
        'client_id',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'packages_id'); 
    }

    // Add this relation to fix your error
    public function findDriverRequests()
    {
        return $this->hasMany(FindDriverRequest::class, 'package_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($package) {
            // Delete related find driver requests manually
            $package->findDriverRequests()->each(function ($request) {
                // Also delete driver offers related to this request
                $request->offers()->delete();
                $request->delete();
            });
        });
    }
}