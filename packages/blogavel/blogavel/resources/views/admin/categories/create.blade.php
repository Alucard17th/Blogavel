@extends('blogavel::admin.layout')

@section('title', 'Blogavel Admin - Create Category')

@section('content')
    <div class="header">
        <div>
            <h1>Create category</h1>
            <p class="hint">Create a category to group related posts.</p>
        </div>
        <div class="actions">
            <a href="{{ route('blogavel.admin.categories.index') }}">
                <button type="button">Back</button>
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('blogavel.admin.categories.store') }}">
        @csrf

        <div class="row cols-2">
            <div>
                <label>Name</label>
                <input name="name" value="{{ old('name') }}" />
                @error('name')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div>
                <label>Slug (optional)</label>
                <input name="slug" value="{{ old('slug') }}" />
                @error('slug')<div class="error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row" style="margin-top:12px">
            <div>
                <label>Parent</label>
                <select name="parent_id">
                    <option value="">(none)</option>
                    @foreach ($parents as $parent)
                        <option value="{{ $parent->id }}" @selected((string) old('parent_id') === (string) $parent->id)>{{ $parent->name }}</option>
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
