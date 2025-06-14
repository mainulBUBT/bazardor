<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Enums\Role;

class ModeratorUserController extends Controller
{
    public function index()
    {
        $moderators = User::where('role', Role::ADMIN)->get();
        return view('admin.moderators.index', compact('moderators'));
    }

    public function create()
    {
        return view('admin.moderators.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $moderator = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => Role::ADMIN,
        ]);
        return redirect()->route('admin.moderators.index')->with('success', 'Moderator created successfully.');
    }

    public function edit(User $moderator)
    {
        return view('admin.moderators.edit', compact('moderator'));
    }

    public function update(Request $request, User $moderator)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $moderator->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        $moderator->name = $validated['name'];
        $moderator->email = $validated['email'];
        if (!empty($validated['password'])) {
            $moderator->password = bcrypt($validated['password']);
        }
        $moderator->save();
        return redirect()->route('admin.moderators.index')->with('success', 'Moderator updated successfully.');
    }

    public function destroy(User $moderator)
    {
        $moderator->delete();
        return redirect()->route('admin.moderators.index')->with('success', 'Moderator deleted successfully.');
    }
} 