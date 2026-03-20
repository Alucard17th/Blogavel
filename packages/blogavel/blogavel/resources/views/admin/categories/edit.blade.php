@extends('blogavel::admin.layout')

@section('title', 'Blogavel Admin - Edit Category')

@section('content')
    <div class="header">
        <div>
            <h1>Edit category</h1>
            <p class="hint">Update the category name, slug, and hierarchy.</p>
        </div>
        <div class="actions">
            <a href="{{ route('blogavel.admin.categories.index') }}">
                <button type="button">Back</button>
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('blogavel.admin.categories.update', $category) }}">
        @csrf
        @method('PUT')

        <div class="row cols-2">
            <div>
                <label>Name</label>
                <input name="name" value="{{ old('name', $category->name) }}" />
                @error('name')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div>
                <label>Slug (optional)</label>
                <input name="slug" value="{{ old('slug', $category->slug) }}" />
                @error('slug')<div class="error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row" style="margin-top:12px">
            <div>
                <label>Parent</label>
                <select name="parent_id">
                    <option value="">(none)</option>
                    @foreach ($parents as $parent)
                        <option value="{{ $parent->id }}" @selected((string) old('parent_id', $category->parent_id) === (string) $parent->id)>{{ $parent->name }}</option>
                    @endforeach
                </select>
                @error('parent_id')<div class="error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="actions" style="margin-top:14px">
            <button type="submit" class="btn-primary">Save</button>
        </div>
    </form>
@endsection
