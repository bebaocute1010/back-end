<?php

namespace App\Models;

use App\Services\PostService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s',
        'deleted_at' => 'datetime:Y-m-d H:m:s',
    ];

    private  $post_service;

    public  function __construct()  {
        $this->post_service = new PostService();
    }

    public function image() {
        return App::make('url')->to('/') . Image::find($this->image_id)->url;
    }

    public function getPost(Request $request) {
        return $this->post_service->getPost($request->id);
    }

    public function user() {
        return User::find($this->user_id);
    }
}
