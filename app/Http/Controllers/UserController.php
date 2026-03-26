<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Section;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display list of users
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::with(['section.department', 'position'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    // 🔍 Full name without middle name
                    $query->whereRaw(
                        "CONCAT(first_name, ' ', last_name) LIKE ?",
                        ["%{$search}%"]
                    )
                    // 🔍 Username
                    ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->orderBy('last_name')
            ->paginate(10)
            ->withQueryString();

        $sections  = Section::with('department')
            ->where('is_active', 1)
            ->orderBy('section_name')
            ->get();

        $positions = Position::where('is_active', 1)
            ->orderBy('position_title')
            ->get();

        // Return AJAX view for live search
        if ($request->ajax()) {
            return view('admin.users.search', compact('users', 'search'))->render();
        }

        return view('admin.users.index', compact('users', 'sections', 'positions'));
    }

    /**
     * Store new user (from modal)
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name'   => 'required|string|max:255',
            'middle_name'   => 'required|string|max:255',
            'last_name'   => 'required|string|max:255',
            'username'    => 'required|string|max:100|unique:users,username',
            'password'    => 'required|string|min:6',
            'section_id'  => 'required|exists:sections,section_id',
            'position_id' => 'required|exists:positions,position_id',
            'role'        => 'required|in:ADMIN,CHIEF,DEPARTMENT-HEAD,SECTION-HEAD,EMPLOYEE',
        ]);

        User::create([
            'first_name'     => $request->first_name,
            'middle_name'   => $request->middle_name,
            'last_name'   => $request->last_name,
            'username'      => $request->username,
            'password_hash' => Hash::make($request->password),
            'section_id'    => $request->section_id,
            'position_id'   => $request->position_id,
            'role'          => $request->role,
            'is_active'     => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Return user data for modal editing (JSON)
     */
    public function edit(User $user)
    {
        return response()->json([
            'id'              => $user->user_id,
            'first_name'      => $user->first_name,
            'middle_name'     => $user->middle_name,
            'last_name'       => $user->last_name,
            'username'        => $user->username,
            'section_id'      => $user->section_id,
            'department_name' => $user->section->department->department_name ?? '',
            'position_id'     => $user->position_id,
            'role'            => $user->role,
            'is_active'       => $user->is_active,
        ]);
    }

    /**
     * Update user (from modal)
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name'   => 'required|string|max:255',
            'middle_name'   => 'required|string|max:255',
            'last_name'   => 'required|string|max:255',
            'username'    => 'required|string|max:100|unique:users,username,' . $user->user_id . ',user_id',
            'section_id'  => 'required|exists:sections,section_id',
            'position_id' => 'required|exists:positions,position_id',
            'role'        => 'required|in:ADMIN,CHIEF,DEPARTMENT-HEAD,SECTION-HEAD,EMPLOYEE',
            'password'    => 'nullable|min:6',
        ]);

        $data = [
            'first_name'     => $request->first_name,
            'middle_name'   => $request->middle_name,
            'last_name'   => $request->last_name,
            'username'    => $request->username,
            'section_id'  => $request->section_id,
            'position_id' => $request->position_id,
            'role'        => $request->role,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->filled('password')) {
            $data['password_hash'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
