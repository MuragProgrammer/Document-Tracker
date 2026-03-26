
@php
function highlight($text, $search) {
    if (!$search) return $text;
    return preg_replace("/($search)/i", '<mark>$1</mark>', $text);
}
@endphp

<table class="table-container">
    <thead>
        <tr>
            <th>Section Name</th>
            <th>Code</th>
            <th>Department</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sections as $section)
        <tr>
            <td>{!! highlight($section->section_name, request('search')) !!}</td>
            <td>{{ $section->section_code }}</td>
            <td>{!! highlight($section->department->department_name ?? '-', request('search')) !!}</td>
            <td>
                <div class="status-toggle-wrapper">
                    <span class="status-label {{ $section->is_active ? 'enabled' : 'disabled' }}">
                        {{ $section->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </td>
            <td>
                <button class="btn btn-edit editSectionBtn"
                    data-section-id="{{ $section->section_id }}"
                    data-name="{{ $section->section_name }}"
                    data-code="{{ $section->section_code }}"
                    data-department="{{ $section->department_id }}"
                    data-active="{{ $section->is_active }}">
                    Edit
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<p class="note">
    <strong>Note:</strong> Editing is for typos only. To change a section, add a new one.
</p>


<div class="pagination-container">
    {{ $sections->links('components.custom-pagination') }}
</div>

