@extends('blogavel::admin.layout')

@section('title', 'Blogavel Admin - Edit Tag')

@section('content')
    <div class="header">
        <div>
            <h1>Edit tag</h1>
            <p class="hint">Rename or adjust the tag slug.</p>
        </div>
        <div class="actions">
            <a href="{{ route('blogavel.admin.tags.index') }}">
                <button type="button">Back</button>
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('blogavel.admin.tags.update', $tag) }}">
        @csrf
        @method('PUT')

        <div class="row cols-2">
            <div>
                <label>Name</label>
                <input name="name" value="{{ old('name', $tag->name) }}" />
                @error('name')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div>
                <label>Slug (optional)</label>
                <input name="slug" value="{{ old('slug', $tag->slug) }}" />
                @error('slug')<div class="error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="actions" style="margin-top:14px">
            <button type="submit" class="btn-primary">Save</button>
        </div>
    </form>
@endsection
