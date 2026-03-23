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
    public function index()
    {
        $users = User::with(['section.department', 'position'])
            ->orderBy('full_name')
            ->get();

        $sections  = Section::with('department')->where('is_active', 1)->orderBy('section_name')->get();
        $positions = Position::where('is_active', 1)->orderBy('position_title')->get();

        return view('admin.users.index', compact('users', 'sections', 'positions'));
    }

    /**
     * Store new user (from modal)
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name'   => 'required|string|max:255',
            'username'    => 'required|string|max:100|unique:users,username',
            'password'    => 'required|string|min:6',
            'section_id'  => 'required|exists:sections,section_id',
            'position_id' => 'required|exists:positions,position_id',
            'role'        => 'required|in:ADMIN,CHIEF,DIVISION-HEAD,SECTION-HEAD,EMPLOYEE',
        ]);

        User::create([
            'full_name'     => $request->full_name,
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
            'full_name'       => $user->full_name,
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
            'full_name'   => 'required|string|max:255',
            'username'    => 'required|string|max:100|unique:users,username,' . $user->user_id . ',user_id',
            'section_id'  => 'required|exists:sections,section_id',
            'position_id' => 'required|exists:positions,position_id',
            'role'        => 'required|in:ADMIN,CHIEF,DIVISION-HEAD,SECTION-HEAD,EMPLOYEE',
            'password'    => 'nullable|min:6',
        ]);

        $data = [
            'full_name'   => $request->full_name,
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
