<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CardLink extends Model
{
    /** @use HasFactory<\Database\Factories\CardLinkFactory> */
    use HasFactory;
    use SoftDeletes;
}
