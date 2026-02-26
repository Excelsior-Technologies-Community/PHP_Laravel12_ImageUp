<?php

namespace App\Traits;

use Intervention\Image\ImageManagerStatic as Image;

trait HasImageUpload
{
    public static function bootHasImageUpload()
    {
        static::saving(function ($model) {

            // check image exists
            if (request()->hasFile('image')) {

                $file = request()->file('image');

                $filename = time().'.'.$file->getClientOriginalExtension();

                $destination = public_path('uploads/'.$filename);

                // resize image automatically
                Image::make($file)
                    ->resize(400, 400)
                    ->save($destination);

                // save path in DB
                $model->image = 'uploads/'.$filename;
            }
        });
    }
}