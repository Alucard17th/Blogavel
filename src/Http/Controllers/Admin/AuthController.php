<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

final class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            if ((bool) config('blogavel.manage_blog_gate', false) && ! Gate::allows('manage-blog')) {
                Auth::logout();

                request()->session()->invalidate();
                request()->session()->regenerateToken();

                return redirect()
                    ->route('blogavel.admin.login')
                    ->withErrors(['email' => 'This account is not authorized to manage the blog.']);
            }

            return redirect()->route('blogavel.admin.posts.index');
        }

        return view('blogavel::admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = (bool) $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors(['email' => 'These credentials do not match our records.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        if ((bool) config('blogavel.manage_blog_gate', false) && ! Gate::allows('manage-blog')) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('blogavel.admin.login')
                ->withErrors(['email' => 'This account is not authorized to manage the blog.']);
        }

        return redirect()->intended(route('blogavel.admin.posts.index'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('blogavel.admin.login');
    }
}
