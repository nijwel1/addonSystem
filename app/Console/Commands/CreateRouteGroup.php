<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateRouteGroup extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:route-group {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new route group';

    /**
     * Execute the console command.
     */

    //----Without stub----
    // public function handle() {
    //     $name           = strtolower( $this->argument( 'name' ) ); // Keep the name lowercase
    //     $controllerName = ucfirst( $name ) . 'Controller'; // Capitalize for the controller

    //     $route = "Route::prefix('$name')->group(function () {\n";
    //     $route .= "    Route::get('/', [$controllerName::class, 'index'])->name('$name.index');\n";
    //     $route .= "    Route::post('/store', [$controllerName::class, 'store'])->name('$name.store');\n";
    //     $route .= "    Route::get('/edit/{id}', [$controllerName::class, 'edit'])->name('$name.edit');\n";
    //     $route .= "    Route::post('/update/{id}', [$controllerName::class, 'update'])->name('$name.update');\n";
    //     $route .= "    Route::delete('/delete/{id}', [$controllerName::class, 'destroy'])->name('$name.destroy');\n";
    //     $route .= "});";

    //     file_put_contents( base_path( 'routes/web.php' ), "\n" . $route, FILE_APPEND );
    //     $this->info( "Route group for '$name' created successfully." );
    // }

    //----With stub----
    // public function handle() {
    //     $name           = strtolower( $this->argument( 'name' ) ); // Keep the name lowercase
    //     $controllerName = ucfirst( $name ) . 'Controller'; // Capitalize for the controller

    //     // Load the stub file
    //     $stub = file_get_contents( app_path( 'stubs/route-group.stub' ) );

    //     // Replace placeholders with actual values
    //     $stub = str_replace( '{{name}}', $name, $stub );
    //     $stub = str_replace( '{{controller}}', $controllerName, $stub );

    //     // Append to routes file
    //     file_put_contents( base_path( 'routes/web.php' ), "\n" . $stub, FILE_APPEND );
    //     $this->info( "Route group for '$name' created successfully." );
    // }

    //----With stub need confirm----
    // public function handle() {
    //     $name           = strtolower( $this->argument( 'name' ) ); // Keep the name lowercase
    //     $controllerName = ucfirst( $name ) . 'Controller'; // Capitalize for the controller

    //     // Check if the route group already exists
    //     $routesFile = base_path( 'routes/web.php' );
    //     $stub       = file_get_contents( app_path( 'stubs/route-group.stub' ) );
    //     $newRoute   = str_replace( ['{{name}}', '{{controller}}'], [$name, $controllerName], $stub );

    //     // Check if the new route already exists
    //     if ( strpos( file_get_contents( $routesFile ), $newRoute ) !== false ) {
    //         $this->warn( "The route group for '$name' already exists." );
    //         if ( !$this->confirm( 'Do you want to overwrite it?' ) ) {
    //             $this->info( 'Operation cancelled.' );
    //             return;
    //         }
    //     }

    //     // Append to routes file
    //     file_put_contents( $routesFile, "\n" . $newRoute, FILE_APPEND );
    //     $this->info( "Route group for '$name' created successfully." );
    // }

    public function handle() {
        $name           = strtolower( $this->argument( 'name' ) ); // Keep the name lowercase
        $controllerName = ucfirst( $name ) . 'Controller'; // Capitalize for the controller

        // Check if the route group already exists
        $routesFile = base_path( 'routes/web.php' );
        $stub       = file_get_contents( app_path( 'stubs/route-group.stub' ) );
        $newRoute   = str_replace( ['{{name}}', '{{controller}}'], [$name, $controllerName], $stub );

        // Check if the new route already exists
        if ( strpos( file_get_contents( $routesFile ), $newRoute ) !== false ) {
            $this->warn( "The route group for '$name' already exists." );
            if ( !$this->confirm( 'Do you want to overwrite it?' ) ) {
                $this->info( 'Operation cancelled.' );
                return;
            }
        }

        // Read the current routes
        $currentRoutes = file_get_contents( $routesFile );

        // Find the position of the last '});'
        $lastPosition = strrpos( $currentRoutes, '// ------ route end -----' );

        // If '});' is found, insert the new route before it
        if ( $lastPosition !== false ) {
            $updatedRoutes = substr( $currentRoutes, 0, $lastPosition ) . "\n" . $newRoute . "\n" . substr( $currentRoutes, $lastPosition );
            file_put_contents( $routesFile, $updatedRoutes );
            $this->info( "Route group for '$name' created successfully." );
        } else {
            $this->warn( "Could not find the ending for the routes file." );
        }
    }

}
