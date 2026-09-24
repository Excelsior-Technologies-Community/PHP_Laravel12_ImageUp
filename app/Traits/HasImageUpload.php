<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

trait HasImageUpload
{
    public static function bootHasImageUpload()
    {
        static::saving(function ($model) {

            /*
             * Check whether an image was uploaded.
             */
            if (request()->hasFile('image')) {

                $file = request()->file('image');

                /*
                 * Generate a unique filename.
                 */
                $filename = Str::uuid() . '.' .
                    $file->getClientOriginalExtension();

                /*
                 * Destination directory.
                 */
                $uploadDirectory = public_path('uploads');

                /*
                 * Create directory if it does not exist.
                 */
                if (!is_dir($uploadDirectory)) {
                    mkdir(
                        $uploadDirectory,
                        0755,
                        true
                    );
                }

                $destination = $uploadDirectory . '/' . $filename;

                /*
                 * Resize image to 400 x 400.
                 */
                Image::make($file)
                    ->resize(400, 400)
                    ->save($destination, 80);

                /*
                 * Save relative image path.
                 */
                $model->image = 'uploads/' . $filename;
            }
        });
    }
}