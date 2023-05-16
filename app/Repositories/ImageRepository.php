<?php

namespace App\Repositories;

use App\Models\Image;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ImageRepository
{
    public function create(string $filename)
    {
        $url = Storage::url($filename);
        return Image::create(['url' => $url]);
    }

    public function update($id, string $filename)
    {
        $image      = Image::find($id);
        $old_url    = $image->url;
        $url        = Storage::url($filename);
        $image->url = $url;
        $image->save();
        return ['image' => $image, 'old_url' => $old_url];
    }
}
