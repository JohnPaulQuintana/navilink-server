<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['user_id', 'name', 'icon', 'is_system', 'published'];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function links()
    {
        return $this->hasMany(Link::class);
    }
}
