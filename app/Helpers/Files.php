<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

/**
 * Class Reply
 * @package App\Classes
 */
class Files
{
    public static function deleteFile($image, $folder)
    {
        $dir = trim($folder, '/');
        $path = $dir . '/' . $image;

        if (!File::exists(public_path($path))) {
            Storage::delete($path);
        }

        return true;
    }

    public static function deleteDirectory($folder)
    {
        $dir = trim($folder);
        Storage::deleteDirectory($dir);
        return true;
    }

    public static function fileUpload($uploadedFile, $name)
    {
        $directory = 'uploads';
        if (is_dir(public_path($directory)) == false) {

            mkdir(public_path($directory));
        }
        $directory = 'uploads';
        if (is_dir(public_path($directory)) == false) {

            mkdir(public_path($directory));
        }
        $tempPath = public_path($directory . '/' . $name);
        move_uploaded_file($uploadedFile, $tempPath);
        return $name;
    }

    public static function defaultUpload($uploadedFile)
    {
        $title = time();
        $uploadedFile->move(public_path('storage'), $title);
        return 'storage/' . $title;
    }

    public static function defaultUploadWithOriginalName($uploadedFile)
    {
        $title = time() . '-' . $uploadedFile->getClientOriginalName();
        $uploadedFile->move(public_path('storage'), $title);
        return 'storage/' . $title;
    }

}
