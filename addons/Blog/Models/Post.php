<?php

namespace Addons\Blog\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model {
    protected $fillable = ['title', 'content'];
}