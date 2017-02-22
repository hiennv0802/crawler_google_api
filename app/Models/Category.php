<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public function images()
    {
        return $this->hasMany(Image::class);
    }
}
