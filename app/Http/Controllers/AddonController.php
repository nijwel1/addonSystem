<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use ZipArchive;

class AddonController extends Controller {
    public function showUploadForm() {
        return view( 'upload' );
    }

    public function uploadAddon( Request $request ) {
        $request->validate( [
            'addon' => 'required|mimes:zip',
        ] );

        $addonFile     = $request->file( 'addon' );
        $addonPath     = storage_path( 'app/addons' );
        $addonFileName = $addonFile->getClientOriginalName();

        // Ensure the storage/addons directory exists
        if ( !File::exists( $addonPath ) ) {
            File::makeDirectory( $addonPath, 0755, true );
        }

        // Move the uploaded file to the storage/addons directory
        $addonFile->move( $addonPath, $addonFileName );

        // Extract the zip file
        $zip = new ZipArchive;
        $zip->open( $addonPath . '/' . $addonFileName );
        $zip->extractTo( base_path( 'addons' ) );
        $zip->close();

        // Remove the uploaded zip file
        File::delete( $addonPath . '/' . $addonFileName );

        // Register the addon in the composer.json and update autoload
        $this->registerAddon();
        $this->registerProvider();

        return redirect()->back()->with( 'success', 'Addon uploaded and installed successfully!' );
    }

    protected function registerAddon() {
        $composerJsonPath = base_path( 'composer.json' );
        $composerJson     = json_decode( file_get_contents( $composerJsonPath ), true );

        if ( !isset( $composerJson['autoload']['psr-4']['Addons\\'] ) ) {
            $composerJson['autoload']['psr-4']['Addons\\'] = 'addons/';
        }

        if ( !isset( $composerJson['repositories'] ) ) {
            $composerJson['repositories'] = [];
        }

        $addonPath         = base_path( 'addons/Blog/composer.json' );
        $addonComposerJson = json_decode( file_get_contents( $addonPath ), true );
        $addonName         = $addonComposerJson['name'];

        $repository = [
            'type' => 'path',
            'url'  => 'addons/Blog',
        ];

        $composerJson['repositories'][]      = $repository;
        $composerJson['require'][$addonName] = '*';

        file_put_contents( $composerJsonPath, json_encode( $composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );

        // Run composer dump-autoload
        // shell_exec( 'composer dump-autoload' );
    }

    protected function registerProvider() {
        $addonServiceProvider = 'Addons\Providers\BlogServiceProvider';

        // Update config/app.php to add the service provider
        $configAppPath = base_path( 'bootstrap/providers.php' );
        $configApp     = file_get_contents( $configAppPath );

        if ( strpos( $configApp, $addonServiceProvider ) === false ) {
            $pattern     = "/('return' => \[)/";
            $replacement = "\n        $addonServiceProvider::class,";

            // dd( $configApp );
            $configApp = preg_replace( $pattern, $replacement, $configApp );

            file_put_contents( $configAppPath, $configApp );
        }

        // Use Symfony Process to run composer dump-autoload
        $composerPath = 'D:\laragon\bin\composer\composer.phar';
        $process      = new Process( [$composerPath, 'dump-autoload'] );
        $process->setWorkingDirectory( base_path() );
        $process->run();

        if ( !$process->isSuccessful() ) {
            throw new ProcessFailedException( $process );
        }
    }

}
