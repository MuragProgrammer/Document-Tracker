<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Position;
use Illuminate\Support\Facades\DB;

class PositionController extends Controller
{
    // Display all positions
    public function index(Request $request)
    {
        $search = $request->input('search');

        $positions = Position::when($search, function ($q) use ($search) {
                $q->where('position_title', 'like', "%{$search}%");
            })
            ->orderBy('position_title')
            ->paginate(10)
            ->withQueryString();

        // If AJAX request → return only table
        if ($request->ajax()) {
            return view('admin.positions.search', compact('positions'))->render();
        }

        return view('admin.positions.index', compact('positions'));
    }

    // Store new position
    public function store(Request $request)
    {
        $request->validate([
            'position_title'    => 'required|string|max:255',
            'is_active'         => 'nullable|boolean',
        ]);

        Position::create([
            'position_title'   => $request->position_title,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('positions.index')->with('success', 'Position added successfully!');
    }

    // Edit form via modal
    public function edit(Position $position)
    {
        return response()->json($position);
    }

    // Update position
    public function update(Request $request, Position $position)
    {
        $request->validate([
            'position_title'    => 'required|string|max:255',
            'is_active'         => 'nullable|boolean',
        ]);

        $position->update([
            'position_title'   => $request->position_title,
            'is_active'        => $request->boolean('is_active'),
        ]);

        return redirect()->route('positions.index')->with('success', 'Position updated successfully!');
    }

    // Delete position
    public function destroy(Position $position)
    {
        $position->delete();
        return redirect()->route('positions.index')->with('success', 'Position deleted successfully!');
    }

    // Toggle status via AJAX
    public function toggleStatus(Position $position)
    {
        $position->update([
            'is_active' => !$position->is_active,
        ]);

        return response()->json([
            'success'   => true,
            'is_active' => $position->is_active,
        ]);
    }

    // Search names for validation/autocomplete
    public function searchNames(Request $request)
    {
        $data = $request->all();

        if (!isset($data['table'], $data['column'], $data['query'])) {
            return response()->json(['results' => []], 400);
        }

        $table  = $data['table'];
        $column = $data['column'];
        $query  = $data['query'];

        // Whitelist tables/columns
        $allowed = [
            'positions' => ['position_title'],
            'departments' => ['department_name', 'department_code'],
            'users'       => ['username', 'email'],
            'sections'    => ['section_name'],
        ];

        if (!isset($allowed[$table]) || !in_array($column, $allowed[$table])) {
            return response()->json(['results' => []], 403);
        }

        $results = DB::table($table)
            ->where($column, 'LIKE', "%$query%")
            ->pluck($column);

        return response()->json(['results' => $results]);
    }
}
