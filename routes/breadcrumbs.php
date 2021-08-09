<?php // routes/breadcrumbs.php

// Note: Laravel will automatically resolve `Breadcrumbs::` without
// this import. This is nice for IDE syntax and refactoring.
use Diglactic\Breadcrumbs\Breadcrumbs;

// This import is also not required, and you could replace `BreadcrumbTrail $trail`
//  with `$trail`. This is nice for IDE type checking and completion.
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// ====================== blog =============================
// Blog
Breadcrumbs::for('blog', function (BreadcrumbTrail $trail) {
    $trail->push('Blog', route('blog.home'));
});

// Blog > Home
Breadcrumbs::for('blogHome', function (BreadcrumbTrail $trail) {
    $trail->parent('blog');
    $trail->push('Home', route('blog.home'));
});

// Blog > Search > [name]
Breadcrumbs::for('blogSearch', function (BreadcrumbTrail $trail, $keyword) {
    $trail->parent('blog');
    $trail->push('Search', route('blog.search'));
    $trail->push($keyword, route('blog.search'));
});

// Blog > Categories > [title]
Breadcrumbs::for('blogPostsCategory', function (BreadcrumbTrail $trail, $category) {
    $trail->parent('blog');
    $trail->push('Categories', route('blog.categories'));
    $trail->push($category->title, '#');
});

// Blog > Categories
Breadcrumbs::for('blogCategories', function (BreadcrumbTrail $trail) {
    $trail->parent('blog');
    $trail->push('Categories', route('blog.categories'));
});

// Blog > Tags
Breadcrumbs::for('blogTags', function (BreadcrumbTrail $trail) {
    $trail->parent('blog');
    $trail->push('Tags', route('blog.tags'));
});

// Blog > Tags > [title]
Breadcrumbs::for('blogPostsTag', function (BreadcrumbTrail $trail, $tag) {
    $trail->parent('blog');
    $trail->push('Tags', route('blog.tags'));
    $trail->push($tag->title, '#');
});

// ====================== dashboard =============================
// Dashboard
Breadcrumbs::for('dashboard', function (BreadcrumbTrail $trail) {
    $trail->push('Dashboard', route('dashboard.index'));
});

// Index Category
Breadcrumbs::for('categories', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Categories', route('categories.index'));
});

// Add Category
Breadcrumbs::for('addCategory', function (BreadcrumbTrail $trail) {
    $trail->parent('categories');
    $trail->push('Add', route('categories.create'));
});

// Edit Category
Breadcrumbs::for('editCategory', function (BreadcrumbTrail $trail, $category) {
    $trail->parent('categories');
    $trail->push('Edit', route('categories.edit', compact('category')));
    $trail->push($category->title, route('categories.edit', compact('category')));
});

// Detail Category
Breadcrumbs::for('detailCategory', function (BreadcrumbTrail $trail, $category) {
    $trail->parent('categories');
    $trail->push('Detail', route('categories.show', compact('category')));
    $trail->push($category->title, route('categories.show', compact('category')));
});

// Tags Index
Breadcrumbs::for('tags', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Tags', route('tags.index'));
});

Breadcrumbs::for('addTags', function (BreadcrumbTrail $trail) {
    $trail->parent('tags');
    $trail->push('Add', route('tags.create'));
});

Breadcrumbs::for('editTags', function (BreadcrumbTrail $trail, $tag) {
    $trail->parent('tags');
    $trail->push('Edit', route('tags.edit', $tag));
    $trail->push($tag->title, route('tags.edit', $tag));
});

// Posts Index
Breadcrumbs::for('posts', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Posts', route('posts.index'));
});

Breadcrumbs::for('addPosts', function (BreadcrumbTrail $trail) {
    $trail->parent('posts');
    $trail->push('Add', route('posts.create'));
});

// Posts Detail
Breadcrumbs::for('detailPost', function (BreadcrumbTrail $trail, $post) {
    $trail->parent('posts');
    $trail->push('Detail', route('posts.show', compact('post')));
    $trail->push($post->title, route('posts.show', compact('post')));
});

Breadcrumbs::for('editPost', function (BreadcrumbTrail $trail, $post) {
    $trail->parent('posts');
    $trail->push('Edit', route('posts.edit', compact('post')));
    $trail->push($post->title, route('posts.edit', compact('post')));
});

// File Manager Index
Breadcrumbs::for('fileManager', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('File Manager', route('fileManager.index'));
});

// Roles Index
Breadcrumbs::for('role', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Roles', route('roles.index'));
});

Breadcrumbs::for('roleDetail', function (BreadcrumbTrail $trail, $role) {
    $trail->parent('role');
    $trail->push('Detail', route('roles.show', compact('role')));
    $trail->push($role->name, route('roles.show', compact('role')));
});

Breadcrumbs::for('addRole', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Add', route('roles.create'));
});

Breadcrumbs::for('editRole', function (BreadcrumbTrail $trail, $role) {
    $trail->parent('role');
    $trail->push('Edit', route('roles.edit', compact('role')));
    $trail->push($role->name, route('roles.edit', compact('role')));
});

// Users Index
Breadcrumbs::for('user', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Users', route('users.index'));
});

Breadcrumbs::for('addUser', function (BreadcrumbTrail $trail) {
    $trail->parent('user');
    $trail->push('Add', route('users.create'));
});

Breadcrumbs::for('editUser', function (BreadcrumbTrail $trail, $user) {
    $trail->parent('user');
    $trail->push('Edit', route('users.edit', compact('user')));
    $trail->push($user->name, route('users.edit', compact('user')));
});
