
<!-- category list -->
@foreach ($categories as $category)
    <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center pr-0">
        <label class="mt-auto mb-auto">
            <!-- todo: show category title -->
            {{ str_repeat('>', $count) . '' .$category->title }}
        </label>
        <div>
            <!-- detail -->
            <a href="#" class="btn btn-sm btn-primary" role="button">
                <i class="fas fa-eye"></i>
            </a>
            <!-- edit -->
            <a class="btn btn-sm btn-info" role="button">
                <i class="fas fa-edit"></i>
            </a>
            <!-- delete -->
            <form class="d-inline" action="" method="POST">
                <button type="submit" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
        <!-- todo:show subcategory -->
        @if ($category->parent)
        @include('categories._categoryList', [
                'categories' => $category->parent,
                'count' => $count + 2,
            ])
        @else
            wwkawalmwlamlwm
        @endif
    </li>
@endforeach
<!-- end  category list -->
