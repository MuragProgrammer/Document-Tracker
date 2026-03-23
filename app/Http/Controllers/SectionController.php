<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class SectionController extends Controller
{
    public function index()
    {
        $sections = Section::with('department')->orderBy('section_id')->get();
        $departments = Department::orderBy('department_name')->get(); // for Add Section form
        return view('admin.sections.index', compact('sections', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'section_name'   => 'required|string|max:255',
            'department_id'  => 'required|exists:departments,department_id',
            'is_active'      => 'nullable|boolean',
        ]);

        Section::create([
            'section_name'  => $request->section_name,
            'department_id' => $request->department_id,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('sections.index')->with('success', 'Section added.');
    }


    public function update(Request $request, Section $section)
    {
        $section->update([
            'name' => $request->name,
            'is_active' => $request->is_active
        ]);


        $request->validate([
            'section_name'   => 'required|string|max:255',
            'department_id'  => 'required|exists:departments,department_id',
            'is_active'      => 'nullable|boolean',
        ]);

        $section->update([
            'section_name'  => $request->section_name,
            'department_id' => $request->department_id,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('sections.index')->with('success', 'Section updated.');
    }

    public function edit(Section $section)
    {
        $departments = Department::orderBy('department_name')->get();
        return view('admin.sections.edit', compact('section', 'departments'));
    }

    public function destroy(Section $section)
    {
        $section->delete();
        return redirect()->route('sections.index')->with('success', 'Section deleted.');
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
}
