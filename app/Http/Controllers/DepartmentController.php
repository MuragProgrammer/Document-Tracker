<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::orderBy('department_id')->get();
        return view('admin.departments.index', compact('departments'));
    }

    public function create (){
        return view('admin.departments.add-dept');
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_name' => 'required|string|max:255',
            'department_code' => 'required|string|max:50|unique:departments,department_code',
            'is_active'       => 'nullable',
        ]);

        Department::create([
            'department_name' => $request->department_name,
            'department_code' => $request->department_code,
            'is_active'       => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('departments.index')->with('success', 'Department added.');
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'department_name' => 'required|string|max:255',
            'department_code' => 'required|string|max:50|unique:departments,department_code,'
                . $department->department_id . ',department_id',
            'is_active' => 'nullable|boolean',
        ]);

        $department->update([
            'department_name' => $request->department_name,
            'department_code' => $request->department_code,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('departments.index')
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('departments.index')->with('success', 'Department deleted.');
    }

    public function searchNames(Request $request)
    {
        $data = $request->all();

        if (!isset($data['table'], $data['column'], $data['query'])) {
            return response()->json(['results' => []], 400);
        }

        $table = $data['table'];
        $column = $data['column'];
        $query = $data['query'];

        // Whitelist tables/columns
        $allowed = [
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


    public function toggleStatus(Department $department)
    {
        $department->update([
            'is_active' => !$department->is_active,
        ]);

        return response()->json([
            'success' => true,
            'is_active' => $department->is_active,
        ]);
    }
}
