<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with('branch')->withCount(['students', 'callLogs'])->latest();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('username', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();
        $branches = Branch::where('status', 'active')->orderBy('name')->get();

        return view('admin.users.index', compact('users', 'branches'));
    }

    public function create(): View
    {
        $branches = Branch::where('status', 'active')->orderBy('name')->get();
        return view('admin.users.create', compact('branches'));
    }

    public function store(Request $request): RedirectResponse
    {
        // Gracefully handle if admin provides username or entered username in email field
        if (!$request->filled('username') && $request->filled('email')) {
            $input = trim($request->email);
            if (!str_contains($input, '@')) {
                $request->merge(['username' => strtolower($input), 'email' => strtolower($input) . '@school.edu']);
            } else {
                $request->merge(['username' => strtolower(explode('@', $input)[0])]);
            }
        } elseif ($request->filled('username') && !$request->filled('email')) {
            $request->merge(['email' => strtolower(trim($request->username)) . '@school.edu']);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:100', 'unique:users,username', 'alpha_dash'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'role' => ['required', 'in:super_admin,branch_admin,admin,staff'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'status' => ['required', 'in:active,inactive'],
            'password' => ['required', 'string', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'branch_id' => $request->branch_id,
            'status' => $request->status,
            'password' => Hash::make($request->password),
        ]);

        ActivityLog::record(
            'created_user',
            "Created new user account for {$user->name} ({$user->role_label})" . ($user->branch ? " assigned to {$user->branch->name}" : ""),
            ['user_id' => $user->id, 'branch_id' => $user->branch_id]
        );

        return redirect()->route('admin.users.index')
            ->with('success', "User account for {$user->name} created successfully.");
    }

    public function edit(User $user): View
    {
        $branches = Branch::where('status', 'active')->orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'branches'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        // Gracefully handle if admin provides username or entered username in email field
        if (!$request->filled('username') && $request->filled('email')) {
            $input = trim($request->email);
            if (!str_contains($input, '@')) {
                $request->merge(['username' => strtolower($input), 'email' => strtolower($input) . '@school.edu']);
            } else {
                $request->merge(['username' => strtolower(explode('@', $input)[0])]);
            }
        } elseif ($request->filled('username') && !$request->filled('email')) {
            $request->merge(['email' => strtolower(trim($request->username)) . '@school.edu']);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:100', Rule::unique('users')->ignore($user->id), 'alpha_dash'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'role' => ['required', 'in:super_admin,branch_admin,admin,staff'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'status' => ['required', 'in:active,inactive'],
            'password' => ['nullable', 'string', Password::defaults()],
        ]);

        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->role = $request->role;
        $user->branch_id = $request->branch_id;
        $user->status = $request->status;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        ActivityLog::record(
            'updated_user',
            "Updated user details for {$user->name}",
            ['user_id' => $user->id]
        );

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} updated successfully.");
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        $statusText = $user->status === 'active' ? 'activated' : 'deactivated';

        ActivityLog::record(
            'toggle_user_status',
            "User {$user->name} was {$statusText}.",
            ['user_id' => $user->id, 'new_status' => $user->status]
        );

        return redirect()->back()->with('success', "User {$user->name} has been {$statusText}.");
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        ActivityLog::record(
            'reset_user_password',
            "Password reset performed for {$user->name}",
            ['user_id' => $user->id]
        );

        return redirect()->back()->with('success', "Password for {$user->name} has been reset successfully.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();

        ActivityLog::record(
            'deleted_user',
            "Deleted user account: {$name}",
            ['user_id' => $user->id]
        );

        return redirect()->route('admin.users.index')
            ->with('success', "User {$name} deleted successfully.");
    }
}
