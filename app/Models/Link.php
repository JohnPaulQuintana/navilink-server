<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $fillable = ['user_id','category_id','title','url','description','image','favicon','domain','issynced','platform','safety_status','visited_date'];
}
