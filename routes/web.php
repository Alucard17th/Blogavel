<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use Blogavel\Blogavel\Models\Category;
use Blogavel\Blogavel\Models\Comment;
use Blogavel\Blogavel\Models\Media;
use Blogavel\Blogavel\Models\Post;
use Blogavel\Blogavel\Models\Tag;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('/blog', function () {
    $prefix = config('blogavel.route_prefix', 'blogavel');

    return Inertia::render('blog/index', [
        'api_base' => url("/api/{$prefix}/v1"),
    ]);
})->name('blog.index');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard/blog', function () {
        $prefix = config('blogavel.route_prefix', 'blogavel');
        $adminPrefix = config('blogavel.admin_prefix', 'admin');

        return Inertia::render('dashboard/blog/index', [
            'blogavel' => [
                'prefix' => $prefix,
                'admin_prefix' => $adminPrefix,
                'admin_base' => url("/{$prefix}/{$adminPrefix}"),
            ],
        ]);
    })->name('dashboard.blog.index');

    Route::get('dashboard/blog/posts', function () {
        $prefix = config('blogavel.route_prefix', 'blogavel');
        $adminPrefix = config('blogavel.admin_prefix', 'admin');

        $posts = Post::query()
            ->with(['category'])
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        return Inertia::render('dashboard/blog/posts', [
            'blogavel' => [
                'admin_base' => url("/{$prefix}/{$adminPrefix}"),
            ],
            'posts' => $posts->map(fn ($post) => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'status' => $post->status,
                'published_at' => optional($post->published_at)?->toISOString(),
                'category' => $post->category ? [
                    'id' => $post->category->id,
                    'name' => $post->category->name,
                ] : null,
            ]),
        ]);
    })->name('dashboard.blog.posts');

    Route::get('dashboard/blog/posts/create', function () {
        $categories = Category::query()->orderBy('name')->get(['id', 'name']);
        $tags = Tag::query()->orderBy('name')->get(['id', 'name']);

        return Inertia::render('dashboard/blog/posts-create', [
            'categories' => $categories,
            'tags' => $tags,
        ]);
    })->name('dashboard.blog.posts.create');

    Route::post('dashboard/blog/posts', function (Request $request) {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,scheduled,published'],
            'published_at' => ['nullable', 'date'],
            'category_id' => ['nullable', 'integer', 'exists:blogavel_categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:blogavel_tags,id'],
        ]);

        $tagIds = $data['tags'] ?? [];
        unset($data['tags']);

        /** @var \Blogavel\Blogavel\Models\Post $post */
        $post = Post::create($data);

        if (count($tagIds) > 0) {
            $post->tags()->sync($tagIds);
        }

        return redirect()->route('dashboard.blog.posts');
    })->name('dashboard.blog.posts.store');

    Route::get('dashboard/blog/posts/{post:id}/edit', function (Post $post) {
        $post->load(['tags']);

        $categories = Category::query()->orderBy('name')->get(['id', 'name']);
        $tags = Tag::query()->orderBy('name')->get(['id', 'name']);

        return Inertia::render('dashboard/blog/posts-edit', [
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'status' => $post->status,
                'published_at' => optional($post->published_at)?->format('Y-m-d H:i:s'),
                'category_id' => $post->category_id,
                'tags' => $post->tags->pluck('id')->values(),
            ],
            'categories' => $categories,
            'tags' => $tags,
        ]);
    })->name('dashboard.blog.posts.edit');

    Route::put('dashboard/blog/posts/{post:id}', function (Request $request, Post $post) {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,scheduled,published'],
            'published_at' => ['nullable', 'date'],
            'category_id' => ['nullable', 'integer', 'exists:blogavel_categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:blogavel_tags,id'],
        ]);

        $tagIds = $data['tags'] ?? [];
        unset($data['tags']);

        $post->update($data);
        $post->tags()->sync($tagIds);

        return redirect()->route('dashboard.blog.posts');
    })->name('dashboard.blog.posts.update');

    Route::delete('dashboard/blog/posts/{post:id}', function (Post $post) {
        $post->tags()->detach();
        $post->delete();

        return redirect()->route('dashboard.blog.posts');
    })->name('dashboard.blog.posts.destroy');

    Route::get('dashboard/blog/categories', function () {
        $prefix = config('blogavel.route_prefix', 'blogavel');
        $adminPrefix = config('blogavel.admin_prefix', 'admin');

        $categories = Category::query()
            ->withCount('posts')
            ->orderBy('name')
            ->limit(100)
            ->get();

        return Inertia::render('dashboard/blog/categories', [
            'blogavel' => [
                'admin_base' => url("/{$prefix}/{$adminPrefix}"),
            ],
            'categories' => $categories->map(fn ($category) => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'posts_count' => (int) ($category->posts_count ?? 0),
            ]),
        ]);
    })->name('dashboard.blog.categories');

    Route::get('dashboard/blog/categories/create', function () {
        return Inertia::render('dashboard/blog/categories-create');
    })->name('dashboard.blog.categories.create');

    Route::post('dashboard/blog/categories', function (Request $request) {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:blogavel_categories,slug'],
        ]);

        Category::create($data);

        return redirect()->route('dashboard.blog.categories');
    })->name('dashboard.blog.categories.store');

    Route::get('dashboard/blog/categories/{category:id}/edit', function (Category $category) {
        return Inertia::render('dashboard/blog/categories-edit', [
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ],
        ]);
    })->name('dashboard.blog.categories.edit');

    Route::put('dashboard/blog/categories/{category:id}', function (Request $request, Category $category) {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:blogavel_categories,slug,'.$category->id],
        ]);

        $category->update($data);

        return redirect()->route('dashboard.blog.categories');
    })->name('dashboard.blog.categories.update');

    Route::delete('dashboard/blog/categories/{category:id}', function (Category $category) {
        $category->delete();

        return redirect()->route('dashboard.blog.categories');
    })->name('dashboard.blog.categories.destroy');

    Route::get('dashboard/blog/tags', function () {
        $prefix = config('blogavel.route_prefix', 'blogavel');
        $adminPrefix = config('blogavel.admin_prefix', 'admin');

        $tags = Tag::query()
            ->withCount('posts')
            ->orderBy('name')
            ->limit(200)
            ->get();

        return Inertia::render('dashboard/blog/tags', [
            'blogavel' => [
                'admin_base' => url("/{$prefix}/{$adminPrefix}"),
            ],
            'tags' => $tags->map(fn ($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
                'slug' => $tag->slug,
                'posts_count' => (int) ($tag->posts_count ?? 0),
            ]),
        ]);
    })->name('dashboard.blog.tags');

    Route::get('dashboard/blog/tags/create', function () {
        return Inertia::render('dashboard/blog/tags-create');
    })->name('dashboard.blog.tags.create');

    Route::post('dashboard/blog/tags', function (Request $request) {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:blogavel_tags,slug'],
        ]);

        Tag::create($data);

        return redirect()->route('dashboard.blog.tags');
    })->name('dashboard.blog.tags.store');

    Route::get('dashboard/blog/tags/{tag:id}/edit', function (Tag $tag) {
        return Inertia::render('dashboard/blog/tags-edit', [
            'tag' => [
                'id' => $tag->id,
                'name' => $tag->name,
                'slug' => $tag->slug,
            ],
        ]);
    })->name('dashboard.blog.tags.edit');

    Route::put('dashboard/blog/tags/{tag:id}', function (Request $request, Tag $tag) {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:blogavel_tags,slug,'.$tag->id],
        ]);

        $tag->update($data);

        return redirect()->route('dashboard.blog.tags');
    })->name('dashboard.blog.tags.update');

    Route::delete('dashboard/blog/tags/{tag:id}', function (Tag $tag) {
        $tag->posts()->detach();
        $tag->delete();

        return redirect()->route('dashboard.blog.tags');
    })->name('dashboard.blog.tags.destroy');

    Route::get('dashboard/blog/media', function () {
        $prefix = config('blogavel.route_prefix', 'blogavel');
        $adminPrefix = config('blogavel.admin_prefix', 'admin');

        $media = Media::query()
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        return Inertia::render('dashboard/blog/media', [
            'blogavel' => [
                'admin_base' => url("/{$prefix}/{$adminPrefix}"),
            ],
            'media' => $media->map(fn ($m) => [
                'id' => $m->id,
                'original_name' => $m->original_name,
                'mime_type' => $m->mime_type,
                'size' => (int) $m->size,
                'url' => Storage::disk($m->disk)->url($m->path),
            ]),
        ]);
    })->name('dashboard.blog.media');

    Route::post('dashboard/blog/media', function (Request $request) {
        $data = $request->validate([
            'file' => ['required', 'file', 'image', 'max:5120'],
        ]);

        /** @var \Illuminate\Http\UploadedFile $file */
        $file = $data['file'];

        $disk = (string) config('blogavel.media_disk', 'public');
        $directory = (string) config('blogavel.media_directory', 'blogavel');

        $path = $file->store($directory, $disk);

        Media::create([
            'disk' => $disk,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => (int) $file->getSize(),
        ]);

        return redirect()->route('dashboard.blog.media');
    })->name('dashboard.blog.media.store');

    Route::delete('dashboard/blog/media/{medium}', function (Media $medium) {
        Storage::disk($medium->disk)->delete($medium->path);
        $medium->delete();

        return redirect()->route('dashboard.blog.media');
    })->name('dashboard.blog.media.destroy');

    Route::get('dashboard/blog/comments', function () {
        $prefix = config('blogavel.route_prefix', 'blogavel');
        $adminPrefix = config('blogavel.admin_prefix', 'admin');

        $comments = Comment::query()
            ->with(['post'])
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        return Inertia::render('dashboard/blog/comments', [
            'blogavel' => [
                'admin_base' => url("/{$prefix}/{$adminPrefix}"),
            ],
            'comments' => $comments->map(fn ($c) => [
                'id' => $c->id,
                'status' => $c->status,
                'author' => $c->authorName(),
                'email' => $c->guest_email,
                'content' => $c->content,
                'post' => $c->post ? [
                    'id' => $c->post->id,
                    'title' => $c->post->title,
                ] : null,
                'created_at' => optional($c->created_at)?->toISOString(),
            ]),
        ]);
    })->name('dashboard.blog.comments');

    Route::post('dashboard/blog/comments/{comment}/approve', function (Comment $comment) {
        $comment->status = 'approved';
        $comment->save();

        return redirect()->route('dashboard.blog.comments');
    })->name('dashboard.blog.comments.approve');

    Route::post('dashboard/blog/comments/{comment}/spam', function (Comment $comment) {
        $comment->status = 'spam';
        $comment->save();

        return redirect()->route('dashboard.blog.comments');
    })->name('dashboard.blog.comments.spam');

    Route::delete('dashboard/blog/comments/{comment}', function (Comment $comment) {
        $comment->delete();

        return redirect()->route('dashboard.blog.comments');
    })->name('dashboard.blog.comments.destroy');

    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

require __DIR__.'/settings.php';
