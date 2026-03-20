@extends('blogavel::admin.layout')

@section('title', 'Blogavel Admin - Edit Post')

@section('content')
    <div class="header">
        <div>
            <h1>Edit post</h1>
            <p class="hint">Update content, publishing status, and metadata.</p>
        </div>
        <div class="actions">
            <a href="{{ route('blogavel.admin.posts.index') }}">
                <button type="button">Back</button>
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('blogavel.admin.posts.update', $post) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row cols-2">
            <div>
                <label>Title</label>
                <input name="title" value="{{ old('title', $post->title) }}" />
                @error('title')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div>
                <label>Slug (optional)</label>
                <input name="slug" value="{{ old('slug', $post->slug) }}" />
                @error('slug')<div class="error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row cols-2" style="margin-top:12px">
            <div>
                <label>Category</label>
                <select name="category_id">
                    <option value="">(none)</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) old('category_id', $post->category_id) === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div>
                <label>Status</label>
                <select name="status">
                    @foreach (['draft','scheduled','published'] as $status)
                        <option value="{{ $status }}" @selected(old('status', $post->status) === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                @error('status')<div class="error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row cols-2" style="margin-top:12px">
            <div>
                <label>Published at</label>
                <input name="published_at" value="{{ old('published_at', optional($post->published_at)->format('Y-m-d H:i:s')) }}" />
                @error('published_at')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div>
                <label>Featured image</label>
                @if ($post->featuredMedia)
                    <div style="margin-bottom:10px">
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk($post->featuredMedia->disk)->url($post->featuredMedia->path) }}" alt="" style="max-height:110px" />
                    </div>
                    <label style="margin:0; display:flex; align-items:center; gap:8px; color:var(--text)">
                        <input style="width:auto" type="checkbox" name="remove_featured_image" value="1" />
                        <span>Remove featured image</span>
                    </label>
                @endif
                <div style="margin-top:10px">
                    <input type="file" name="featured_image" />
                    @error('featured_image')<div class="error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="row" style="margin-top:12px">
            <div>
                <label>Tags</label>
                @php($selectedTags = (array) old('tags', $post->tags->pluck('id')->all()))
                <div class="actions">
                    @foreach ($tags as $tag)
                        <label style="margin:0; display:flex; align-items:center; gap:8px; color:var(--text)">
                            <input style="width:auto" type="checkbox" name="tags[]" value="{{ $tag->id }}" @checked(in_array((string) $tag->id, array_map('strval', $selectedTags), true)) />
                            <span>{{ $tag->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('tags')<div class="error">{{ $message }}</div>@enderror
                @error('tags.*')<div class="error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row" style="margin-top:12px">
            <div>
                <label>Content</label>
                <textarea name="content" rows="10">{{ old('content', $post->content) }}</textarea>
                @error('content')<div class="error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="actions" style="margin-top:14px">
            <button type="submit" class="btn-primary">Save</button>
        </div>
    </form>
@endsection
