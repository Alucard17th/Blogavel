@extends('blogavel::admin.layout')

@section('title', 'Blogavel Admin - Create Tag')

@section('content')
    <div class="header">
        <div>
            <h1>Create tag</h1>
            <p class="hint">Use tags to make content easier to browse.</p>
        </div>
        <div class="actions">
            <a href="{{ route('blogavel.admin.tags.index') }}">
                <button type="button">Back</button>
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('blogavel.admin.tags.store') }}">
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

        <div class="actions" style="margin-top:14px">
            <button type="submit" class="btn-primary">Save</button>
        </div>
    </form>
@endsection
