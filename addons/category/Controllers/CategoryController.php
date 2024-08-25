<?php

namespace Addons\Category\Controllers;

use App\Http\Controllers\Controller;

class CategoryController extends Controller {
    public function index() {
        return view( 'Post::category.index' );
    }

    public function create() {
        return view( 'Post::category.create' );
    }
}