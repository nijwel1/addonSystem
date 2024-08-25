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
        // Validate the uploaded file
        $request->validate( [
            'addon' => 'required|mimes:zip',
        ] );

        $addonFile     = $request->file( 'addon' );
        $addonPath     = storage_path( 'app/addons' );
        $addonFileName = $addonFile->getClientOriginalName();

        // Ensure the storage/app/addons directory exists
        if ( !File::exists( $addonPath ) ) {
            File::makeDirectory( $addonPath, 0755, true );
        }

        // Move the uploaded ZIP to the storage/app/addons directory
        $addonFile->move( $addonPath, $addonFileName );

        // Path to the uploaded ZIP file
        $zipPath = $addonPath . '/' . $addonFileName;

        // Initialize variables
        $addonFolderName = null;

        // Open the ZIP file
        $zip = new ZipArchive;
        if ( $zip->open( $zipPath ) === TRUE ) {
            // Collect top-level directories in the ZIP
            $topLevelDirs = [];
            for ( $i = 0; $i < $zip->numFiles; $i++ ) {
                $stat      = $zip->statIndex( $i );
                $fileName  = $stat['name'];
                $pathParts = explode( '/', $fileName );

                // Skip files at the root of the ZIP
                if ( count( $pathParts ) < 2 ) {
                    continue;
                }

                $topLevelDir = $pathParts[0];

                if ( !in_array( $topLevelDir, $topLevelDirs ) ) {
                    $topLevelDirs[] = $topLevelDir;
                }
            }

            // Ensure there's exactly one top-level directory
            if ( count( $topLevelDirs ) !== 1 ) {
                $zip->close();
                // Delete the uploaded ZIP file
                File::delete( $zipPath );
                return redirect()->back()->withErrors( ['addon' => 'The ZIP file must contain exactly one top-level directory.'] );
            }

            // Assign the top-level directory name
            $addonFolderName = $topLevelDirs[0];

            // Extract the ZIP to the 'addons' directory
            $zip->extractTo( base_path( 'addons' ) );
            $zip->close();
        } else {
            // Unable to open the ZIP file
            // Delete the uploaded ZIP file
            File::delete( $zipPath );
            return redirect()->back()->withErrors( ['addon' => 'Failed to open the ZIP file.'] );
        }

        // Delete the uploaded ZIP file after extraction
        File::delete( $zipPath );

        // Proceed only if a folder name was detected
        if ( $addonFolderName ) {
            // Register the addon and its service provider
            $this->registerAddon( $addonFolderName );
            $this->registerProvider( $addonFolderName );

            return redirect()->back()->with( 'success', 'Addon uploaded and installed successfully!' );
        } else {
            return redirect()->back()->withErrors( ['addon' => 'Failed to detect the addon folder name.'] );
        }
    }

    // public function uploadAddon( Request $request ) {
    //     $request->validate( [
    //         'addon' => 'required|mimes:zip',
    //     ] );

    //     $addonFile     = $request->file( 'addon' );
    //     $addonPath     = storage_path( 'app/addons' );
    //     $addonFileName = $addonFile->getClientOriginalName();

    //     // Ensure the storage/addons directory exists
    //     if ( !File::exists( $addonPath ) ) {
    //         File::makeDirectory( $addonPath, 0755, true );
    //     }

    //     // Move the uploaded file to the storage/addons directory
    //     $addonFile->move( $addonPath, $addonFileName );

    //     // Extract the zip file
    //     $zip = new ZipArchive;
    //     $zip->open( $addonPath . '/' . $addonFileName );
    //     $zip->extractTo( base_path( 'addons' ) );
    //     $zip->close();

    //     // Remove the uploaded zip file
    //     File::delete( $addonPath . '/' . $addonFileName );

    //     // Register the addon in the composer.json and update autoload
    //     $this->registerAddon();
    //     $this->registerProvider();

    //     return redirect()->back()->with( 'success', 'Addon uploaded and installed successfully!' );
    // }

    // protected function registerAddon() {
    //     $composerJsonPath = base_path( 'composer.json' );
    //     $composerJson     = json_decode( file_get_contents( $composerJsonPath ), true );

    //     if ( !isset( $composerJson['autoload']['psr-4']['Addons\\'] ) ) {
    //         $composerJson['autoload']['psr-4']['Addons\\'] = 'addons/';
    //     }

    //     if ( !isset( $composerJson['repositories'] ) ) {
    //         $composerJson['repositories'] = [];
    //     }

    //     $addonPath         = base_path( 'addons/Blog/composer.json' );
    //     $addonComposerJson = json_decode( file_get_contents( $addonPath ), true );
    //     $addonName         = $addonComposerJson['name'];

    //     $repository = [
    //         'type' => 'path',
    //         'url'  => 'addons/Blog',
    //     ];

    //     $composerJson['repositories'][]      = $repository;
    //     $composerJson['require'][$addonName] = '*';

    //     file_put_contents( $composerJsonPath, json_encode( $composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );

    //     // Run composer dump-autoload
    //     // shell_exec( 'composer dump-autoload' );
    // }

    protected function registerAddon( $addonFolderName ) {
        $addonPath = base_path( "addons/{$addonFolderName}" );

        // Find the addon composer.json
        $addonComposerPath = "{$addonPath}/composer.json";
        if ( !file_exists( $addonComposerPath ) ) {
            throw new \Exception( "composer.json not found in the addon directory: {$addonFolderName}" );
        }

        $addonComposerJson = json_decode( file_get_contents( $addonComposerPath ), true );
        $addonName         = $addonComposerJson['name'];

        $composerJsonPath = base_path( 'composer.json' );
        $composerJson     = json_decode( file_get_contents( $composerJsonPath ), true );

        // Update the PSR-4 autoload section
        if ( !isset( $composerJson['autoload']['psr-4']["Addons\\"] ) ) {
            $composerJson['autoload']['psr-4']["Addons\\"] = 'addons/';
        }

        // Ensure the repositories section exists
        if ( !isset( $composerJson['repositories'] ) ) {
            $composerJson['repositories'] = [];
        }

        // Check if the repository with the given URL already exists
        $repositoryExists = false;
        foreach ( $composerJson['repositories'] as $repository ) {
            if ( $repository['type'] === 'path' && $repository['url'] === "addons/{$addonFolderName}" ) {
                $repositoryExists = true;
                break;
            }
        }

        // Add the repository for the addon if it doesn't exist
        if ( !$repositoryExists ) {
            $repository = [
                'type' => 'path',
                'url'  => "addons/{$addonFolderName}",
            ];
            $composerJson['repositories'][] = $repository;
        }

        // Add the addon to the require section
        $composerJson['require'][$addonName] = '*';

        // Write the updated composer.json
        file_put_contents( $composerJsonPath, json_encode( $composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );

        // Optionally run composer dump-autoload using Symfony Process (if needed)
        // $this->runComposerDumpAutoload();
    }

    // protected function registerProvider() {
    //     $addonServiceProvider = 'Addons\Providers\BlogServiceProvider';

    //     // Update config/app.php to add the service provider
    //     $configAppPath = base_path( 'bootstrap/providers.php' );
    //     $configApp     = file_get_contents( $configAppPath );

    //     if ( strpos( $configApp, $addonServiceProvider ) === false ) {
    //         $pattern     = "/('return' => \[)/";
    //         $replacement = "\n        $addonServiceProvider::class,";

    //         // dd( $configApp );
    //         $configApp = preg_replace( $pattern, $replacement, $configApp );

    //         file_put_contents( $configAppPath, $configApp );
    //     }

    //     // Use Symfony Process to run composer dump-autoload
    //     $composerPath = 'D:\laragon\bin\composer\composer.phar';
    //     $process      = new Process( [$composerPath, 'dump-autoload'] );
    //     $process->setWorkingDirectory( base_path() );
    //     $process->run();

    //     if ( !$process->isSuccessful() ) {
    //         throw new ProcessFailedException( $process );
    //     }
    // }

    protected function registerProvider( $addonFolderName ) {
        // Construct the expected service provider class name
        $providerClass = "Addons\\{$addonFolderName}\\{$addonFolderName}ServiceProvider";

        // Path to the bootstrap/providers.php file
        $providersPath = base_path( 'bootstrap/providers.php' );

        // Load the existing providers array
        $providers = include $providersPath;

        // Check if the provider is already registered
        if ( !in_array( $providerClass, $providers ) ) {
            // Add the new provider to the array
            $providers[] = $providerClass;

            // Convert the array back to PHP code
            $providersContent = "<?php\n\nreturn [\n";
            foreach ( $providers as $provider ) {
                $providersContent .= "    {$provider}::class,\n";
            }
            $providersContent .= "];\n";

            // Write the updated providers array back to the file
            File::put( $providersPath, $providersContent );
        }

        // Run composer dump-autoload to refresh the autoloader
        $this->runComposerDumpAutoload();
    }

    protected function runComposerDumpAutoload() {
        $composerPath = 'D:\laragon\bin\composer\composer.phar';
        $process      = new Process( [$composerPath, 'dump-autoload'] );
        $process->setWorkingDirectory( base_path() );
        $process->run();

        if ( !$process->isSuccessful() ) {
            throw new ProcessFailedException( $process );
        }
    }

}
