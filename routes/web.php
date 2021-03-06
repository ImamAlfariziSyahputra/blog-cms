<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileManagerController;
use App\Http\Controllers\LocalizationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get(
    '/localization/{language}', 
    [LocalizationController::class, 'switchLang']
)->name('localization.switch');

Route::get('/', [BlogController::class, 'home'])->name('blog.home');
Route::get('/post/{slug}', [BlogController::class, 'postDetail'])->name('blog.post.detail');
Route::get('/search', [BlogController::class, 'searchPosts'])->name('blog.search');
Route::get('/categories', [BlogController::class, 'showCategories'])->name('blog.categories');
Route::get('/categories/{slug}', [BlogController::class, 'showPostsByCategory'])->name('blog.posts.category');
Route::get('/tags', [BlogController::class, 'showTags'])->name('blog.tags');
Route::get('/tags/{slug}', [BlogController::class, 'showPostsByTag'])->name('blog.posts.tag');

Auth::routes([
    'register' => false,
]);

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth']], function() {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    
    // Categories
    Route::get('/categories/select', [CategoryController::class, 'select'])
        ->name('categories.select');
    Route::resource('/categories', CategoryController::class);

    // Tags
    Route::resource('/tags', TagController::class)->except(['show']);

    // Posts
    Route::get('/tags/select', [TagController::class, 'selectInput'])
        ->name('tags.select');
    Route::resource('/posts', PostController::class);

    // File Manager
    Route::group(['prefix' => 'filemanager'], function () {
        Route::get('/index', [FileManagerController::class, 'index'])->name('fileManager.index');
        \UniSharp\LaravelFilemanager\Lfm::routes();
    });

    // Roles
    Route::resource('/roles', RoleController::class);

    // User
    Route::get('/users/select', [UserController::class, 'selectInput'])
    ->name('users.select');;
    Route::resource('/users', UserController::class)->except(['show']);
});

