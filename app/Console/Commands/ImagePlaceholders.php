<?php

namespace App\Console\Commands;

use File;
use Illuminate\Console\Command;

class ImagePlaceholders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'image:placeholders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate image placeholders';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Placeholder images base url and storage path
        $url = 'http://via.placeholder.com/';
        $storagePath = storage_path('app/uploads/placeholders/');

        // Remove old placeholders if any
        File::delete(File::glob($storagePath . '*.png'));

        $resolutions = getImageResolutions();
        $resolutions['default'] = '1920x1080';

        // Create placeholder images for default and app specific resolutions
        foreach ($resolutions as $key => $resolution) {
            $filename = $key === 'default'
                ? 'placeholder.png' // Default placeholder
                : 'placeholder-' . $resolution . '.png';

            file_put_contents($storagePath . $filename, file_get_contents($url . $resolution));
        }

        return 1;
    }
}