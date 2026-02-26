<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImageUpload;

class Product extends Model
{
    use HasImageUpload;

    protected $fillable = [
        'name',
        'image'
    ];
}