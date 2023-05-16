<?php

namespace App\Services;

use App\Repositories\ImageRepository;
use Illuminate\Support\Facades\File;

class ImageService
{
    private $image_repository;

    public function __construct()
    {
        $this->image_repository = new ImageRepository();
    }

    public function create(string $filename)
    {
        return $this->image_repository->create($filename);
    }

    public function update($id, string $filename)
    {
        $updated       = $this->image_repository->update($id, $filename);
        $imagePath     = parse_url($updated['old_url'], PHP_URL_PATH);
        $imageFullPath = public_path($imagePath);
        if (File::exists($imageFullPath)) {
            File::delete($imageFullPath);
        }
        return $updated['image'];
    }
}
