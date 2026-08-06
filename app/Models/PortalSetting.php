<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalSetting extends Model
{
    protected $fillable = [
        'portal_name',
        'homepage_message',
        'security_code',
        'logo',
        'favicon',
        'maintenance',
    ];
}