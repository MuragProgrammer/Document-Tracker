<?php

namespace App\Observers;

use App\Models\Section;
use App\Models\User;

class SectionObserver
{
    public function updated(Section $section)
    {
        // Only run if is_active changed
        if (!$section->wasChanged('is_active')) {
            return;
        }

        // Update users under the section
        User::where('section_id', $section->section_id)
            ->update(['is_active' => $section->is_active]);
    }
}
