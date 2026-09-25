<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

trait HasImageUpload
{
    public static function bootHasImageUpload()
    {
        static::saving(function ($model) {

            if (!request()->hasFile('image')) {
                return;
            }

            $file = request()->file('image');

            /*
            |--------------------------------------------------------------------------
            | Unique Filename
            |--------------------------------------------------------------------------
            */

            $filename =
                Str::uuid() .
                '.' .
                strtolower(
                    $file->getClientOriginalExtension()
                );

            /*
            |--------------------------------------------------------------------------
            | Upload Directory
            |--------------------------------------------------------------------------
            */

            $uploadDirectory =
                public_path('uploads');

            if (!is_dir($uploadDirectory)) {

                mkdir(
                    $uploadDirectory,
                    0755,
                    true
                );
            }

            $destination =
                $uploadDirectory .
                '/' .
                $filename;

            /*
            |--------------------------------------------------------------------------
            | Resize Image
            |--------------------------------------------------------------------------
            */

            Image::make($file)
                ->resize(
                    400,
                    400
                )
                ->save(
                    $destination,
                    80
                );

            /*
            |--------------------------------------------------------------------------
            | Save Relative Path
            |--------------------------------------------------------------------------
            */

            $model->image =
                'uploads/' .
                $filename;
        });
    }
}