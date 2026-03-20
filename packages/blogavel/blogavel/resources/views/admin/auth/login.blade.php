@extends('blogavel::admin.layout')

@section('title', 'Blogavel Admin - Login')

@section('content')
    <div class="header">
        <div>
            <h1>Sign in</h1>
            <p class="hint">Sign in to manage your Blogavel content.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('blogavel.admin.login.store') }}">
        @csrf

        <div class="row cols-2">
            <div>
                <label>Email</label>
                <input name="email" value="{{ old('email') }}" autocomplete="username" />
                @error('email')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div>
                <label>Password</label>
                <input type="password" name="password" value="" autocomplete="current-password" />
                @error('password')<div class="error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="actions" style="margin-top:12px">
            <label style="margin:0; display:flex; align-items:center; gap:8px; color:var(--text)">
                <input style="width:auto" type="checkbox" name="remember" value="1" @checked(old('remember')) />
                <span>Remember me</span>
            </label>
        </div>

        <div class="actions" style="margin-top:14px">
            <button type="submit" class="btn-primary">Login</button>
        </div>
    </form>
@endsection
