<?php

namespace Addons\Blog\Controllers;

use App\Http\Controllers\Controller;

class BlogController extends Controller {
    public function index() {
        return view( 'Post::blog.index' );
    }

    public function create() {
        return view( 'Post::blog.create' );
    }
}