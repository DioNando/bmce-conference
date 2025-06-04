<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdministratorController extends Controller
{
    /**
     * Display a listing of administrators.
     */
    public function index()
    {
        $administrators = User::role(UserRole::ADMIN->value)->paginate(10);
        return view('admin.administrators.index', compact('administrators'));
    }

    /**
     * Show the form for creating a new administrator.
     */
    public function create()
    {
        return view('admin.administrators.create');
    }

    /**
     * Store a newly created administrator in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'first_name' => $validated['first_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole(UserRole::ADMIN->value);

        return redirect()->route('admin.administrators')
                        ->with('success', 'Administrator created successfully.');
    }

    /**
     * Show the form for editing the specified administrator.
     */
    public function edit(User $administrator)
    {
        return view('admin.administrators.edit', compact('administrator'));
    }

    /**
     * Update the specified administrator in storage.
     */
    public function update(Request $request, User $administrator)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $administrator->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $administrator->name = $validated['name'];
        $administrator->email = $validated['email'];

        if (!empty($validated['password'])) {
            $administrator->password = Hash::make($validated['password']);
        }

        $administrator->save();

        return redirect()->route('admin.administrators')
                        ->with('success', 'Administrator updated successfully.');
    }

    /**
     * Remove the specified administrator from storage.
     */
    public function destroy(User $administrator)
    {
        // Empêcher la suppression du dernier administrateur
        $adminCount = User::role(UserRole::ADMIN->value)->count();
        if ($adminCount <= 1) {
            return redirect()->route('admin.administrators')
                            ->with('error', 'Cannot delete the last administrator.');
        }

        $administrator->delete();

        return redirect()->route('admin.administrators')
                        ->with('success', 'Administrator deleted successfully.');
    }
}
