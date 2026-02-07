<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMediaProvider extends Model
{
    use HasFactory;
    protected $fillable = ['name'];
    protected $table = 'social__media__providers'; // map the actual table name
}
