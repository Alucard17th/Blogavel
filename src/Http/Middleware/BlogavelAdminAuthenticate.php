<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

final class BlogavelAdminAuthenticate
{
    /**
     * @param  Closure(Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            return $next($request);
        }

        $request->session()->put('url.intended', $request->fullUrl());

        return redirect()->route('blogavel.admin.login');
    }
}
