<?php

namespace Addons\Category;

use Illuminate\Support\ServiceProvider;

class CategoryServiceProvider extends ServiceProvider {
    public function boot() {
        $this->loadViewsFrom( __DIR__ . '/Views', 'Post' );
        $this->loadMigrationsFrom( __DIR__ . '/Migrations' );
        $this->loadRoutesFrom( __DIR__ . '/routes/web.php' );
    }

    public function register() {
        $this->app->make( 'Addons\Category\Controllers\CategoryController' );
    }
}