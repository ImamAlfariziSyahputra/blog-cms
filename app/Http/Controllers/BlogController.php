<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
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

    public function searchPosts(Request $request)
    {
        if(!$request->get('keyword')) {
            return redirect()->route('blog.home');
        }

        $posts = Post::publish()->search($request->keyword)->paginate($this->perPage);

        return view('blog.searchPost', [
            'posts' => $posts->withQueryString(),
        ]);
    }

    public function showCategories()
    {
        $categories = Category::onlyParent()->paginate($this->perPage);

        return view('blog.categories', [
            'categories' => $categories->withQueryString(),
        ]);
    }

    public function showTags()
    {
        $tags = Tag::paginate($this->perPage);

        return view('blog.tags', [
            'tags' => $tags->withQueryString(),
        ]);
    }

    public function showPostsByCategory($slug)
    {
        $posts = Post::publish()->whereHas('category', function($query) use($slug) {
            return $query->where('slug', $slug);
        })->paginate($this->perPage);

        $category = Category::where('slug', $slug)->first();
        $categoryRoot = $category->root();

        return view('blog.posts-categories', [
            'posts' => $posts,
            'category' => $category,
            'categoryRoot' => $categoryRoot,
        ]);
    }

    public function showPostsByTag($slug)
    {
        $posts = Post::publish()->whereHas('tag', function($query) use($slug) {
            return $query->where('slug', $slug);
        })->paginate($this->perPage);

        $tag = Tag::where('slug', $slug)->first();
        $tags = Tag::search($tag->title)->get();

        return view('blog.posts-tag', [
            'posts' => $posts,
            'tag' => $tag,
            'tags' => $tags,
        ]);
    }
}
