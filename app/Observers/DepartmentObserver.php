<?php

namespace App\Observers;

use App\Models\Department;
use App\Models\Section;
use App\Models\User;

class DepartmentObserver
{
    public function updated(Department $department)
    {
        // Only run if is_active changed
        if (!$department->wasChanged('is_active')) {
            return;
        }

        // Update sections under department
        Section::where('department_id', $department->department_id)
            ->update(['is_active' => $department->is_active]);

        // Get all section IDs
        $sectionIds = Section::where('department_id', $department->department_id)->pluck('section_id');

        // Update users under those sections
        User::whereIn('section_id', $sectionIds)
            ->update(['is_active' => $department->is_active]);
    }
}
