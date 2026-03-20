@extends('blogavel::admin.layout')

@section('title', 'Blogavel Admin - Create Post')

@section('content')
    <div class="header">
        <div>
            <h1>Create post</h1>
            <p class="hint">Draft, schedule, or publish. You can always edit later.</p>
        </div>
        <div class="actions">
            <a href="{{ route('blogavel.admin.posts.index') }}">
                <button type="button">Back</button>
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('blogavel.admin.posts.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="row cols-2">
            <div>
                <label>Title</label>
                <input name="title" value="{{ old('title') }}" />
                @error('title')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div>
                <label>Slug (optional)</label>
                <input name="slug" value="{{ old('slug') }}" />
                @error('slug')<div class="error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row cols-2" style="margin-top:12px">
            <div>
                <label>Category</label>
                <select name="category_id">
                    <option value="">(none)</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) old('category_id') === (string) $category->id)>
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
                        <option value="{{ $status }}" @selected(old('status','draft') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                @error('status')<div class="error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row cols-2" style="margin-top:12px">
            <div>
                <label>Published at</label>
                <input name="published_at" value="{{ old('published_at') }}" />
                @error('published_at')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div>
                <label>Featured image</label>
                <input type="file" name="featured_image" />
                @error('featured_image')<div class="error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row" style="margin-top:12px">
            <div>
                <label>Tags</label>
                @php($selectedTags = (array) old('tags', []))
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
                <textarea name="content" rows="10">{{ old('content') }}</textarea>
                @error('content')<div class="error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="actions" style="margin-top:14px">
            <button type="submit" class="btn-primary">Save</button>
        </div>
    </form>
@endsection
