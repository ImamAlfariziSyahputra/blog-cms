@extends('layouts.dashboard')

@section('title')
    {{ trans('categories.title.edit') }}
@endsection

@section('breadcrumbs')
{{ Breadcrumbs::render('editCategoryTitle', $category) }}
@endsection
@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <!-- title -->
                    <div class="form-group">
                        <label for="input_category_title" class="font-weight-bold">
                            {{ trans('categories.form.input.title.label') }}
                        </label>
                    <input 
                        id="input_category_title" 
                        name="title" 
                        type="text" 
                        class="form-control @error('title') is-invalid @enderror" 
                        placeholder="{{ trans('categories.form.input.title.placeholder') }}" 
                        value="{{ old('title', $category->title) }}"
                    />
                    @error('title')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    </div>
                    <!-- slug -->
                    <div class="form-group">
                        <label for="input_category_slug" class="font-weight-bold">
                            {{ trans('categories.form.input.slug.label') }}
                        </label>
                    <input 
                        id="input_category_slug" 
                        name="slug" 
                        type="text" 
                        class="form-control @error('slug') is-invalid @enderror" 
                        readonly
                        placeholder="{{ trans('categories.form.input.slug.placeholder') }}" 
                        value="{{ old('slug', $category->slug) }}"
                    />
                        @error('slug')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <!-- thumbnail -->
                    <div class="form-group">
                        <label for="input_category_thumbnail" class="font-weight-bold">
                            {{ trans('categories.form.input.thumbnail.label') }}
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <button 
                                    id="button_category_thumbnail" 
                                    data-input="input_category_thumbnail" 
                                    data-preview="holder"
                                    class="btn btn-primary" 
                                    type="button"
                                >
                                    {{ trans('categories.button.browse.value') }}
                                </button>
                            </div>
                            <input 
                                id="input_category_thumbnail" 
                                name="thumbnail" 
                                type="text" 
                                class="form-control @error('thumbnail') is-invalid @enderror" 
                                placeholder="{{ trans('categories.form.input.thumbnail.placeholder') }}"
                                readonly 
                                value="{{ old('thumbnail', asset($category->thumbnail)) }}"
                            />
                            @error('thumbnail')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    {{-- preview thumbnail --}}
                    <div id="holder">
                    </div>
                    <!-- parent_category -->
                    <div class="form-group">
                        <label for="select_category_parent" class="font-weight-bold">
                            {{ trans('categories.form.select.parent_category.label') }}
                        </label>
                        <select 
                            id="select_category_parent" 
                            name="parent_category" 
                            data-placeholder="{{ trans('categories.form.select.parent_category.placeholder') }}" 
                            class="custom-select w-100"
                        >
                        @if (old('parent_category', $category->parent))
                            <option value="{{ old('parent_category', $category->parent)->id }}" selected>
                                {{ old('parent_category', $category->parent)->title }}
                            </option>
                        @endif
                        </select>
                    </div>
                    <!-- description -->
                    <div class="form-group">
                        <label for="input_category_description" class="font-weight-bold">
                            {{ trans('categories.form.textarea.description.label') }}
                        </label>
                        <textarea 
                            id="input_category_description" 
                            name="description" 
                            class="form-control @error('description') is-invalid @enderror" 
                            rows="3"
                            placeholder="{{ trans('categories.form.textarea.description.placeholder') }}"
                        >{{ old('description', $category->description) }}</textarea>
                        @error('description')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="float-right">
                        <a 
                            class="btn btn-warning text-white px-4" 
                            href="{{ route('categories.index') }}"
                        >
                            {{ trans('categories.button.back.value') }}
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            {{ trans('categories.button.edit.value') }}
                        </button>
                    </div>                
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('cssExternal')
    <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
@endpush

@push('jsExternal')
    <script src="{{asset('vendor/select2/js/select2.min.js') }}"></script>
    <script src="{{asset('vendor/select2/js/i18n/'. app()->getLocale() .'.js') }}"></script>
    {{-- File Manager --}}
    <script src="{{ asset('vendor/laravel-filemanager/js/stand-alone-button.js') }}"></script>
@endpush

@push('jsInternal')
    <script>
        $(function() {
            // Generate Slug
            function generateSlug(value){
                return value.trim()
                .toLowerCase()
                .replace(/[^a-z\d-]/gi, '-')
                .replace(/-+/g, '-').replace(/^-|-$/g, "");
            } 

            //parent category
            $('#select_category_parent').select2({
                theme: 'bootstrap4',
                language: "{{ app()->getLocale() }}",
                allowClear: true,
                ajax: {
                    url: "{{ route('categories.select') }}",
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: item.title,
                                    id: item.id
                                }
                            })
                        };
                    }
                }
            });

            // input title event
            $('#input_category_title').change(function() {
                let title = $(this).val();
                let parentCategory = $('#select_category_parent').val() ?? '';
                $('#input_category_slug').val(generateSlug(title + '-' + parentCategory));
            });
            
            // input select parentCategory event
            $('#select_category_parent').change(function() {
                let title = $('#input_category_title').val();
                let parentCategory = $(this).val() ?? '';
                $('#input_category_slug').val(generateSlug(title + '-' + parentCategory));
            });

            // thumbnail input event
            $('#button_category_thumbnail').filemanager('image');
        });
    </script>
@endpush