<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class PostController extends Controller
{
    // 'manage_posts' => [
    //     'post_show',
    //     'post_create',
    //     'post_update',
    //     'post_detail',
    //     'post_delete'
    // ],
    public function __construct()
    {
        $this->middleware('permission:post_show', ['only' => 'index']);
        $this->middleware('permission:post_create', ['only' => ['create','store']]);
        $this->middleware('permission:post_update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:post_detail', ['only' => 'show']);
        $this->middleware('permission:post_delete', ['only' => 'destroy']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $statusSelected = in_array($request->get('status'), ['publish', 'draft']) 
            ? $request->get('status')
            : 'publish';

        $posts = $statusSelected == 'publish' 
            ? Post::publish() 
            : Post::draft();

        $perPage = 5;

        if($request->get('keyword')) {
            $posts->search($request->get('keyword'));
        }

        return view('posts.index', [
            'posts' => $posts->paginate($perPage)->withQueryString(),
            'statuses' => $this->statuses(),
            'statusSelected' => $statusSelected,
        ]);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create', [
            'categories' => Category::with('herit')->onlyParent()->get(),
            'statuses' => $this->statuses(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'title' => 'required|string|max:60',
                'slug' => 'required|string|unique:posts,slug',
                'thumbnail' => 'required',
                'description' => 'required|string|max:240',
                'content' => 'required',
                'category' => 'required',
                'tag' => 'required',
                'status' => 'required'
            ],
            [],
            $this->customAttributes(),
        );

        // if validate fails
        if($validator->fails()) {
            // Insert again data Tag with Old Data
            if($request['tag']) {
                $request['tag'] = Tag::select('id', 'title')->whereIn('id', $request->tag)->get();
            }
            return redirect()->back()->withInput($request->all())->withErrors($validator);
        }

        DB::beginTransaction();

        try {
            $post = Post::create([
                'title' => $request->title,
                'slug' => $request->slug,
                'thumbnail' => parse_url($request->thumbnail)['path'],
                'description' => $request->description,
                'content' => $request->content,
                'category' => $request->category,
                'tag' => $request->tag,
                'status' => $request->status,
                'user_id' => Auth::user()->id,
            ]);

            $post->tag()->attach($request->tag);
            $post->category()->attach($request->category);

            Alert::success(
                trans('posts.alert.create.title'),
                trans('posts.alert.create.message.success'),
            );

            return redirect()->route('posts.index');
        } catch (\Throwable $th) {
            DB::rollBack();

            Alert::error(
                trans('posts.alert.create.title'),
                trans('posts.alert.create.message.error', ['error' => $th->getMessage()]),
            );

            // Insert again data Tag with Old Data
            if($request['tag']) {
                $request['tag'] = Tag::select('id', 'title')->whereIn('id', $request->tag)->get();
            }

            return redirect()->back()->withInput($request->all());
        } finally {
            DB::commit();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        $categories = $post->category;
        $tags = $post->tag;
        return view('posts.show', compact('post', 'categories', 'tags'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        return view('posts.edit', [
            'post' => $post,
            'categories' => Category::with('herit')->onlyParent()->get(),
            'statuses' => $this->statuses(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'title' => 'required|string|max:60',
                'slug' => 'required|string|unique:posts,slug,' . $post->id,
                'thumbnail' => 'required',
                'description' => 'required|string|max:240',
                'content' => 'required',
                'category' => 'required',
                'tag' => 'required',
                'status' => 'required'
            ],
            [],
            $this->customAttributes(),
        );

        // if validate fails
        if($validator->fails()) {
            // Insert again data Tag with Old Data
            if($request['tag']) {
                $request['tag'] = Tag::select('id', 'title')->whereIn('id', $request->tag)->get();
            }
            return redirect()->back()->withInput($request->all())->withErrors($validator);
        }

        DB::beginTransaction();

        try {
            $post->update([
                'title' => $request->title,
                'slug' => $request->slug,
                'thumbnail' => parse_url($request->thumbnail)['path'],
                'description' => $request->description,
                'content' => $request->content,
                'category' => $request->category,
                'tag' => $request->tag,
                'status' => $request->status,
                'user_id' => Auth::user()->id,
            ]);

            $post->tag()->sync($request->tag);
            $post->category()->sync($request->category);

            Alert::success(
                trans('posts.alert.update.title'),
                trans('posts.alert.update.message.success'),
            );

            return redirect()->route('posts.index');
        } catch (\Throwable $th) {
            DB::rollBack();

            Alert::error(
                trans('posts.alert.update.title'),
                trans('posts.alert.update.message.error', ['error' => $th->getMessage()]),
            );

            // Insert again data Tag with Old Data
            if($request['tag']) {
                $request['tag'] = Tag::select('id', 'title')->whereIn('id', $request->tag)->get();
            }

            return redirect()->back()->withInput($request->all());
        } finally {
            DB::commit();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        DB::beginTransaction();

        try {
            $post->tag()->detach();
            $post->category()->detach();

            $post->delete();


            Alert::success(
                trans('posts.alert.delete.title'),
                trans('posts.alert.delete.message.success'),
            );
        } catch (\Throwable $th) {
            DB::rollBack();

            Alert::error(
                trans('posts.alert.delete.title'),
                trans('posts.alert.delete.message.error', ['error' => $th->getMessage()]),
            );
        } finally {
            DB::commit();

            return redirect()->back();
        }
    }

    private function statuses()
    {
        return [
            'draft' => trans('posts.form.select.status.option.draft'),
            'publish' => trans('posts.form.select.status.option.publish'),
        ];
    }

    private function customAttributes()
    {
        return [
            'title' => trans('posts.form.input.title.attribute'),
            'slug' => trans('posts.form.input.slug.attribute'),
            'thumbnail' => trans('posts.form.input.thumbnail.attribute'),
            'description' => trans('posts.form.textarea.description.attribute'),
            'content' => trans('posts.form.textarea.content.attribute'),
            'category' => trans('posts.form.input.category.attribute'),
            'tag' => trans('posts.form.select.tag.attribute'),
            'status' => trans('posts.form.select.status.attribute'),
        ];
    }
}
