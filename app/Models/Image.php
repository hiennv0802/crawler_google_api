<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = ['link', 'name', 'category_id'];

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }
}
