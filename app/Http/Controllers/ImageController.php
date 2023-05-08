<?php

namespace App\Http\Controllers;

use App\Models\Image;
use http\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function create(string $filename) {
        $url = Storage::url( $filename);
        return Image::create(['url' => $url]);
    }

    public function update($id, string $filename) {
        $image = Image::find($id);
        $old_url = $image->url;
        $url = Storage::url( $filename);
        $image->url = $url;
        $image->save();
        $imagePath = parse_url($old_url, PHP_URL_PATH);
        $imageFullPath = public_path($imagePath);
        if (File::exists($imageFullPath)) {
            File::delete($imageFullPath);
        }
        return $image;
    }
}
