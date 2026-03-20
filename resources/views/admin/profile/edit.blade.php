@extends('blogavel::admin.layout')

@section('title', 'Blogavel Admin - Profile')

@section('content')
    <div class="header">
        <div>
            <h1>Profile</h1>
            <p class="hint">Update your account details used to access Blogavel admin.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('blogavel.admin.profile.update') }}">
        @csrf
        @method('PUT')

        <div class="row cols-2">
            <div>
                <label>Name</label>
                <input name="name" value="{{ old('name', $user->name) }}" />
                @error('name')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div>
                <label>Email</label>
                <input name="email" value="{{ old('email', $user->email) }}" />
                @error('email')<div class="error">{{ $message }}</div>@enderror
            </div>
        </div>

        <hr />

        <div class="row cols-2">
            <div>
                <label>New password (optional)</label>
                <input type="password" name="password" value="" autocomplete="new-password" />
                @error('password')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div>
                <label>Confirm new password</label>
                <input type="password" name="password_confirmation" value="" autocomplete="new-password" />
            </div>
        </div>

        <div class="actions" style="margin-top:14px">
            <button type="submit" class="btn-primary">Save</button>
        </div>
    </form>
@endsection
