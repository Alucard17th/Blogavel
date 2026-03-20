<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

final class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();
        if ($user === null) {
            abort(403);
        }

        return view('blogavel::admin.profile.edit', [
            'user' => $user,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        if ($user === null) {
            abort(403);
        }

        $table = method_exists($user, 'getTable') ? (string) $user->getTable() : 'users';
        $keyName = method_exists($user, 'getKeyName') ? (string) $user->getKeyName() : 'id';

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique($table, 'email')->ignore($user->{$keyName}, $keyName),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];

        if (isset($data['password']) && (string) $data['password'] !== '') {
            $user->password = Hash::make((string) $data['password']);
        }

        $user->save();

        return redirect()
            ->route('blogavel.admin.profile.edit')
            ->with('status', 'Profile updated.');
    }
}
