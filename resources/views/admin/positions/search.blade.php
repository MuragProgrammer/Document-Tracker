@php
    function highlight($text, $search)
    {
        if (!$search) {
            return $text;
        }
        return preg_replace("/($search)/i", '<mark>$1</mark>', $text);
    }
@endphp

<table class="table-container">
    <thead>
        <tr>
            <th>Position</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($positions as $position)
            <tr>
                <td>{!! highlight($position->position_title, request('search')) !!}</td>
                <td>
                    <div class="status-toggle-wrapper">
                        <span class="status-label {{ $position->is_active ? 'enabled' : 'disabled' }}">
                            {{ $position->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </td>
                <td>
                    <button class="btn btn-edit editPositionBtn" data-position-id="{{ $position->position_id }}"
                        data-position-title="{{ $position->position_title }}" data-active="{{ $position->is_active }}">
                        Edit
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<p class="note">
    <strong>Note:</strong> Editing is for misspellings or typos only. If a position has changed, add a new one.
</p>
<div class="pagination-container">
    {{ $positions->links('components.custom-pagination') }}
</div>
