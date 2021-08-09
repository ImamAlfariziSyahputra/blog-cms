<ul>
    @foreach ($categoryRoot as $item)
    <li>
        @if ($category->slug == $item->slug)
        {{ $item->title }}
        @else
        <a href="{{ route('blog.posts.category', ['slug' => $item->slug]) }}">
            {{ $item->title }}
        </a>
        @endif
        @if ($item->herit)
            @include('blog.sub-categories', [
                'categoryRoot' => $item->herit,
                'category' => $category
            ])
        @endif
    </li>
    @endforeach
</ul>