<?php

namespace Addons\Blog;

use Illuminate\Support\ServiceProvider;

class BlogServiceProvider extends ServiceProvider {
    public function boot() {
        $this->loadViewsFrom( __DIR__ . '/Views', 'Post' );
        $this->loadMigrationsFrom( __DIR__ . '/Migrations' );
        $this->loadRoutesFrom( __DIR__ . '/routes/web.php' );
    }

    public function register() {
        $this->app->make( 'Addons\Blog\Controllers\BlogController' );
    }
}