<?php

namespace App\CustomCasts;

use File;
use Image;
use Vkovic\LaravelCustomCasts\CustomCastBase;

abstract class ImageCastBase extends CustomCastBase
{
    /**
     * Callback
     *
     * @var
     */
    protected $storeImagesCallback;

    /**
     * Storage dir (relative to app/storage)
     *
     * @return mixed
     */
    abstract static function storageDir();

    /**
     * Image sizes e.g.: ['lg' => '1024x768', 'xl' => '1280x1024]
     *
     * @return array
     */
    abstract static function imageSizes();

    /**
     * @param $value
     *
     * @return string String to be inserted into database
     *
     * @throws \Exception
     */
    public function setAttribute($value)
    {
        // New value to be saved in corresponding db field
        $newValue = null;

        // Handle base64 image string (uploading via Backpack)
        if (starts_with($value, 'data:image')) {
            $extension = $this->getBase64fileExtension($value);
            $filename = str_random(16);

            // Make image object from base64 string
            $image = Image::make($value);
            $newValue = static::storageDir() . '/' . $filename . '.' . $extension;

            // Make sure images are saved after model is saved,
            // not here (when attribute is set)
            $this->storeImagesCallback = function () use ($image, $filename, $extension, $newValue) {
                $imageQuality = 75;
                $originalImage = clone $image;

                // Store original
                $image->save(storage_path('app/' . $newValue), $imageQuality);

                // Store other image sizes
                foreach (static::imageSizes() as $imageSize) {
                    list($width, $height) = explode('x', $imageSize);
                    $optimalImageQuality = $this->optimizeImageQuality($width, $height, $imageQuality);

                    // Variant image name and path
                    $variantName = $filename . '-' . $imageSize . '.' . $extension;
                    $variantRelPath = static::storageDir() . '/' . $variantName;

                    // Save image with defined quality
                    (clone $originalImage)->fit($width, $height)
                        ->save(storage_path('app/' . $variantRelPath), $optimalImageQuality);
                }
            };
        } elseif (!is_null($newValue)) {
            throw new \InvalidArgumentException('Image needs to be base64 encoded string');
        }

        return $newValue;
    }

    /**
     * Get extension from base64 string
     *
     * @param $base64string
     *
     * @throws \Exception
     *
     * @return string
     */
    protected function getBase64fileExtension($base64string)
    {
        $start = strpos($base64string, '/') + 1;
        $end = strpos($base64string, ';');

        if ($start === false || $end === false) {
            throw new \Exception('Can`t get extension from base64 encoded string');
        }

        return substr($base64string, $start, $end - $start);
    }

    /**
     * Optimize image quality.
     * This is needed because if we set quality to e.g 75
     * small images (less than 300x300px) quality will be bad
     *
     * @param     $width
     * @param     $height
     * @param int $baseQuality
     *
     * @return int
     *
     */
    protected function optimizeImageQuality($width, $height, $baseQuality)
    {
        $minResolution = 300 * 300;
        $maxResolution = 1000 * 1000;
        $resolution = $width * $height;

        if ($resolution >= $maxResolution) {
            return $baseQuality;
        } elseif ($resolution <= $minResolution) {
            return 100;
        } else {
            return ceil(100 - ($resolution - $minResolution) * 0.000027473);
        }
    }

    /**
     * React to created event
     *
     * Handle initial image saving
     */
    public function created()
    {
        if (is_callable($this->storeImagesCallback)) {
            ($this->storeImagesCallback)();

            // It appears that this callback somehow does not get unset properly,
            // so, we'll fo it manually.
            // It takes lots of memory, especially while seeding.
            // This cold be due to cloning in callback or callback itself ...
            unset($this->storeImagesCallback);
        }
    }

    /**
     * React to updating event
     *
     * Handle image replacing here, because we can check if corresponding
     * model field is dirty and update accordingly
     */
    public function updating()
    {
        // Check if image field is changed and if callback is defined
        if ($this->model->isDirty($this->attribute)) {
            // Delete old file
            $this->deleteImages();

            // Callback to save file on model updating
            if (is_callable($this->storeImagesCallback)) {
                ($this->storeImagesCallback)();
            }
        }
    }

    /**
     * Reach to deleted event
     *
     * Handle image deleting
     */
    public function deleted()
    {
        // Delete related images when model is deleted
        $this->deleteImages();
    }

    /**
     * Delete original images as well as other variations (dimensions)
     */
    protected function deleteImages()
    {
        $sourceImagePath = $this->model->getOriginal($this->attribute);

        if ($sourceImagePath && File::exists(storage_path('app/' . $sourceImagePath))) {
            $pathInfo = pathinfo($sourceImagePath);
            $dir = $pathInfo['dirname'];
            $filename = $pathInfo['filename'];

            $pattern = storage_path('app/' . $dir) . '/' . $filename . '*';

            File::delete(File::glob($pattern));
        }
    }
}