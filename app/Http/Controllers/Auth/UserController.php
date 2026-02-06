<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the users with search + pagination.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::where('is_admin', 0) 
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'asc') // earliest registered first
            ->paginate(10); // 10 users per page

        return view('auth.users.index', compact('users', 'search'));
    }

    /**
     * Show form to create a new user.
     */
    public function create()
    {
        return view('auth.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password), // ✅ Secure password
        ]);

        return redirect()->route('auth.users.index')
                         ->with('success', '✅ User created successfully.');
    }

    /**
     * Display a specific user details.
     */
    public function show(User $user)
    {
        return view('auth.users.show', compact('user'));
    }

    /**
     * Show the form for editing a user.
     */
    public function edit(User $user)
    {
        return view('auth.users.edit', compact('user'));
    }

    /**
     * Update an existing user in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6', // ✅ Optional password
        ]);

        $user->name  = $request->name;
        $user->email = $request->email;

        // ✅ Only update password if filled
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->route('auth.users.index')
                         ->with('success', '✅ User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('auth.users.index')
                         ->with('success', '🗑️ User deleted successfully.');
    }
}
