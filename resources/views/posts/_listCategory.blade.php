<ul class="pl-1 my-1" style="list-style: none;">
    @foreach ($categories as $category)
        <li class="form-group form-check my-1">
            @if (
                    $categoryChecked
                    && 
                    in_array($category->id, $categoryChecked)
                )
                <input 
                    class="form-check-input"
                    type="checkbox" 
                    value="{{ $category->id }}"
                    name="category[]" checked>
            @else
                <input 
                class="form-check-input"
                type="checkbox" 
                value="{{ $category->id }}"
                name="category[]">
            @endif

            <label class="form-check-label">{{ $category->title }}</label>
            @if ($category->herit)
                @include('posts._listCategory', [
                    'categories' => $category->herit,
                    'categoryChecked' => $categoryChecked,
                ])
            @endif
        </li>
    @endforeach
</ul>