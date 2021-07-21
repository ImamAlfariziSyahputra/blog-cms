<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\TryCatch;
use RealRashid\SweetAlert\Facades\Alert;

class CategoryController extends Controller
{
    // 'manage_categories' => [
    //     'category_show',
    //     'category_create',
    //     'category_update',
    //     'category_detail',
    //     'category_delete'
    // ],
    public function __construct()
    {
        $this->middleware('permission:category_show', ['only' => 'index']);
        $this->middleware('permission:category_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:category_update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:category_detail', ['only' => 'show']);
        $this->middleware('permission:category_delete', ['only' => 'destroy']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = Category::with('herit');

        if($request->has('keyword') && trim($request->keyword)) {
            $categories->search($request->keyword);
        } else {
            $categories->onlyParent();
        }

        return view(
            'categories.index', 
            [
                'categories' => $categories->paginate(5)
                    ->appends(['keyword' => $request->get('keyword')]),
            ]
        );
    }

    public function select(Request $request)
    {
        $categories = [];

        if ($request->has('q')) {
            $search = $request->q;
            $categories = Category::select('id', 'title')
                ->where('title', 'LIKE', "%$search%")
                ->limit(6)
                ->get();
        } else {
            $categories = Category::select('id', 'title')->onlyParent()->limit(6)->get();
        }

        return response()->json($categories);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make(
            $request->all(), 
            [
                'title' => 'required|string|max:60',
                'slug' => 'required|string|unique:categories,slug',
                'thumbnail' => 'required',
                'description' => 'required|string|max:240',
            ],
            [], //!message
            $this->customAttributes(),
        );

        // Validate Select Input give back the id and title
        if($validator->fails()) {
            if($request->has('parent_category')) {
                $request['parent_category'] = Category::select('id', 'title')
                    ->find($request->parent_category);
            }

            return redirect()->back()
                ->withInput($request->all())
                ->withErrors($validator);
        }

        // *parse to remove http://localhost/
        $parsedThumbnail = parse_url($request->thumbnail)['path'];

        // Insert Data
        try {
            Category::create([
                'title' => $request->title,
                'slug' => $request->slug,
                'thumbnail' => $parsedThumbnail,
                'description' => $request->description,
                'parent_id' => $request->parent_category,
            ]);

            Alert::success(
                trans('categories.alert.create.title'),
                trans('categories.alert.create.message.success'),
            );
            return redirect()->route('categories.index');
        } catch(\Throwable $th) {
            if($request->has('parent_category')) {
                $request['parent_category'] = Category::select('id', 'title')
                    ->find($request->parent_category);
            }

            Alert::error(
                trans('categories.alert.create.title'),
                trans('categories.alert.create.message.error', ['error' => $th->getMessage()]),
            );
            return redirect()->back()
                ->withInput($request->all())
                ->withErrors($validator);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        // Validation
        $validator = Validator::make(
            $request->all(), 
            [
                'title' => 'required|string|max:60',
                'slug' => 'required|string|unique:categories,slug,'.$category->id,
                'thumbnail' => 'required',
                'description' => 'required|string|max:240',
            ],
            [], //!message
            $this->customAttributes(),
        );

        // Validate Select Input give back the id and title
        if($validator->fails()) {
            if($request->has('parent_category')) {
                $request['parent_category'] = Category::select('id', 'title')
                    ->find($request->parent_category);
            }

            return redirect()->back()
                ->withInput($request->all())
                ->withErrors($validator);
        }

        // *parse to remove http://localhost/
        $parsedThumbnail = parse_url($request->thumbnail)['path'];

        // Update Data
        try {
            $category->update([
                'title' => $request->title,
                'slug' => $request->slug,
                'thumbnail' => $parsedThumbnail,
                'description' => $request->description,
                'parent_id' => $request->parent_category,
            ]);

            Alert::success(
                trans('categories.alert.update.title'),
                trans('categories.alert.update.message.success'),
            );
            return redirect()->route('categories.index');
        } catch(\Throwable $th) {
            if($request->has('parent_category')) {
                $request['parent_category'] = Category::select('id', 'title')
                    ->find($request->parent_category);
            }

            Alert::error(
                trans('categories.alert.update.title'),
                trans('categories.alert.update.message.error', ['error' => $th->getMessage()]),
            );
            return redirect()->back()
                ->withInput($request->all());
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        try {
            $category->delete();
            Alert::success(
                trans('categories.alert.delete.title'),
                trans('categories.alert.delete.message.success'),
            );
        } catch (\Throwable $th) {
            Alert::error(
                trans('categories.alert.delete.title'),
                trans('categories.alert.delete.message.error', ['error' => $th->getMessage()]),
            );
        }

        return redirect()->back();
    }

    private function customAttributes()
    {
        return [
            'title' => trans('categories.form.input.title.attribute'),
            'slug' => trans('categories.form.input.slug.attribute'),
            'thumbnail' => trans('categories.form.input.thumbnail.attribute'),
            'description' => trans('categories.form.textarea.description.attribute'),
        ];
    }
}
