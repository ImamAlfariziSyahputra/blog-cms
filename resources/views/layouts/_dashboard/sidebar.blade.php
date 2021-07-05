
<nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
    <div class="sb-sidenav-menu">
        <div class="nav">
            {{-- Dashboard --}}
            <a class="nav-link {{ setActive('dashboard.index') }}" href="{{ route('dashboard.index') }}">
                <div class="sb-nav-link-icon">
                <i class="fas fa-tachometer-alt"></i>
                </div>
                {{ trans('dashboard.link.dashboard') }}
            </a>
            <div class="sb-sidenav-menu-heading">
                {{ trans('dashboard.menu.master') }}
            </div>
            {{-- Posts --}}
            <a 
                class="nav-link {{ setActive([
                    'posts.index'
                ]) }}" 
                href="{{ route('posts.index') }}"
            >
                <div class="sb-nav-link-icon">
                    <i class="far fa-newspaper"></i>
                </div>
                {{ trans('dashboard.link.posts') }}
            </a>
            {{-- Categories --}}
            <a 
                class="nav-link 
                {{ setActive([
                    'categories.index', 'categories.create', 'categories.edit', 'categories.show'
                ]) }}
                " 
                href="{{ route('categories.index') }}"
            >
                <div class="sb-nav-link-icon">
                <i class="fas fa-bookmark"></i>
                </div>
                {{ trans('dashboard.link.categories') }}
            </a>
            {{-- Tags --}}
            <a 
                class="nav-link
                {{ setActive([
                    'tags.index', 'tags.create', 'tags.edit'
                ]) }}
                " 
                href="{{ route('tags.index') }}"
            >
                <div class="sb-nav-link-icon">
                    <i class="fas fa-tags"></i>
                </div>
                {{ trans('dashboard.link.tags') }}
            </a>
            <div class="sb-sidenav-menu-heading">
                {{ trans('dashboard.menu.user_permission') }}
            </div>
            <a class="nav-link" href="#">
                <div class="sb-nav-link-icon">
                <i class="fas fa-user"></i>
                </div>
                {{ trans('dashboard.link.users') }}
            </a>
            <a class="nav-link" href="#">
                <div class="sb-nav-link-icon">
                <i class="fas fa-user-shield"></i>
                </div>
                {{ trans('dashboard.link.roles') }}
            </a>
            <div class="sb-sidenav-menu-heading">
                {{ trans('dashboard.menu.setting') }}
            </div>
            <a class="nav-link" href="#">
                <div class="sb-nav-link-icon">
                <i class="fas fa-photo-video"></i>
                </div>
                {{ trans('dashboard.link.file_manager') }}
            </a>
        </div>
    </div>
    <div class="sb-sidenav-footer">
        <div class="small">
            {{ trans('dashboard.label.logged_in_as') }}:
        </div>
        <!-- show username -->
        {{ Auth::user()->name }}
    </div>
</nav>
 