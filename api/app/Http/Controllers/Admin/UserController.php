<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\User\Models\User;
use App\Mail\AdminInviteMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $perPage = (int) $request->integer('per_page', 15);

        $users = User::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'ILIKE', "%$q%")
                        ->orWhere('email', 'ILIKE', "%$q%")
                        ->orWhere('region', 'ILIKE', "%$q%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'string'],
            'region' => ['nullable', 'string', 'max:255'],
            'language' => ['nullable', 'string', 'max:10'],
        ]);

        $inviteToken = Str::random(40);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Str::password(16), // placeholder; to be reset on accept
            'region' => $data['region'] ?? null,
            'language' => $data['language'] ?? null,
            'invite_token' => $inviteToken,
            'invited_at' => now(),
            'is_active' => true,
        ]);

        // Assign role if using spatie/permission
        if (method_exists($user, 'assignRole')) {
            $user->assignRole($data['role']);
        }

        // Send mockable email
        Mail::to($user->email)->send(new AdminInviteMail($user));

        return response()->json(['id' => $user->id, 'invite_token' => $inviteToken], 201);
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'city' => ['sometimes', 'nullable', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:255'],
            'region' => ['sometimes', 'nullable', 'string', 'max:255'],
            'language' => ['sometimes', 'nullable', 'string', 'max:10'],
            'profile_image' => ['sometimes', 'nullable', 'file', 'image', 'max:5120'],
        ]);

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images');
            $data['profile_image_path'] = $path;
            unset($data['profile_image']);
        }

        $user->fill($data);
        $user->save();

        return response()->json($user);
    }
}
