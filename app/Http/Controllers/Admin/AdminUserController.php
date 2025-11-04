<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'FirstName' => 'required|string|max:255',
            'LastName' => 'required|string|max:255',
            'Email' => 'required|email|max:255|unique:Users,Email',
            'UserName' => 'nullable|string|max:255|unique:Users,UserName',
            'PublicName' => 'nullable|string|max:255',
            'password' => 'required|string|min:6|max:15|confirmed',
            'RoleID' => 'required|integer|in:1,2', // 1 = Admin, 2 = Regular User
            'StatusID' => 'required|integer|in:1,2', // 1 = Active, 2 = Inactive (adjust based on your UserStatus table)
        ]);

        $user = new User();
        $user->FirstName = $validated['FirstName'];
        $user->LastName = $validated['LastName'];
        $user->Email = $validated['Email'];
        $user->UserName = $validated['UserName'] ?? $validated['Email'];
        $user->PublicName = $validated['PublicName'] ?? $validated['FirstName'] . ' ' . $validated['LastName'];
        $user->password = Hash::make($validated['password']);
        $user->RoleID = $validated['RoleID'];
        $user->StatusID = $validated['StatusID'];
        $user->UUID = (string) Str::uuid();
        
        // Admins don't need email verification, automatically verify for admins
        if ($validated['RoleID'] == 1) {
            $user->email_verified_at = now();
        } else {
            $user->email_verified_at = $request->has('email_verified') ? now() : null;
        }
        
        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'FirstName' => 'required|string|max:255',
            'LastName' => 'required|string|max:255',
            'Email' => 'required|email|max:255|unique:Users,Email,' . $user->UserID . ',UserID',
            'UserName' => 'nullable|string|max:255|unique:Users,UserName,' . $user->UserID . ',UserID',
            'PublicName' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6|max:15|confirmed',
            'RoleID' => 'required|integer|in:1,2',
            'StatusID' => 'required|integer|in:1,2',
        ]);

        $user->FirstName = $validated['FirstName'];
        $user->LastName = $validated['LastName'];
        $user->Email = $validated['Email'];
        $user->UserName = $validated['UserName'] ?? $validated['Email'];
        $user->PublicName = $validated['PublicName'] ?? $validated['FirstName'] . ' ' . $validated['LastName'];
        $user->RoleID = $validated['RoleID'];
        $user->StatusID = $validated['StatusID'];

        // Update password only if provided
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        // Handle email verification
        // Admins don't need email verification, automatically verify for admins
        if ($validated['RoleID'] == 1) {
            $user->email_verified_at = now();
        } else {
            if ($request->has('email_verified')) {
                if (!$user->email_verified_at) {
                    $user->email_verified_at = now();
                }
            } else {
                $user->email_verified_at = null;
            }
        }

        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting yourself
        if ($user->UserID == Auth::user()->getAuthIdentifier()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
