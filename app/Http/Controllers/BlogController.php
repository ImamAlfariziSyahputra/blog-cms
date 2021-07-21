<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    private $perPage = 10;

    public function home()
    {
        $posts = Post::publish()->latest()->paginate($this->perPage);
        return view('blog.home', [
            'posts' => $posts,
        ]);
    }

    public function showCategories()
    {
        $categories = Category::onlyParent()->paginate($this->perPage);

        return view('blog.categories', [
            'categories' => $categories,
        ]);
    }
}
