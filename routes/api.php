<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
   Route::prefix('auth')->controller(AuthController::class)->group(function () {
       Route::get('logout', 'logout')->name('logout');
       Route::get('refresh', 'refresh');
       Route::get('me', 'me');
       Route::post('change-password', 'changePassword');
   });

   Route::prefix('post')->controller(PostController::class)->group(function () {
       Route::get('/get', 'getPost');
       Route::get('/get-posts', 'postsOfUser');
       Route::post('/create', 'create');
       Route::post('/update', 'update');
       Route::get('/delete', 'delete');
       Route::post('/delete-multiple', 'deleteMultiple');
   });

   Route::prefix('user-actions')->controller(UserController::class)->group(function () {
       Route::post('mark-read-all', 'markReadAll');
       Route::get('notifications', 'getNotifications');
       Route::post('update-profile','updateProfile');
   });

   Route::middleware('admin')->prefix('admin')->controller(UserController::class)->group(function () {
       Route::get('listUsers', 'getListUsers');
       Route::get('getUser', 'getUserByID');
       Route::get('deleteUser', 'deleteUser');
       Route::post('createUser', 'createUser');
       Route::post('delete-multiple-users', 'deleteMultipleUsers');
       Route::post('update-user', 'updateUser');
   });
});

Route::middleware('guest')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('login', 'login');
        Route::post('signup', 'signUp');
    });
});

Route::controller(PostController::class)->group(function () {
    Route::get('get-all-posts', 'getAllPostsByGuest');
    Route::get('get-post-by-slug', 'getPostBySlug');
});

