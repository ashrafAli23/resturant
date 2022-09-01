<?php


namespace App\Traits;

use Illuminate\Http\Request;

/**
 *  Handle Files Uploades
 */
trait UploadFiles
{
    public function uploadImage(Request $request, $path)
    {
        $imageName = $request->file("image")->getClientOriginalName();
        $path = $request->file("image")->storeAs($path, rand(1, 99999) . $imageName, 'public');
        return asset('storage/' . $path);
        // return asset('storage/app/public/' . $path);
    }

    public function deleteFile($path)
    {
        unlink($path);
    }
}