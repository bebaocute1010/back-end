<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Services\ImageService;
use http\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    private $image_service;

    public function __construct() {
        $this->image_service = new ImageService();
    }

    public function create(string $filename) {
        return $this->image_service->create($filename);
    }

    public function update($id, string $filename) {
        return $this->image_service->update($id, $filename);
    }
}
