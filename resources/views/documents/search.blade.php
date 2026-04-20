@php
// Global highlight helper (only declare once in helpers.php, not in Blade)
if (!function_exists('highlight')) {
    function highlight($text, $search) {
        if (empty($search)) return e($text);
        $escapedText = e($text);
        $escapedSearch = preg_quote($search, '/');
        return preg_replace("/($escapedSearch)/i", '<mark>$1</mark>', $escapedText);
    }
}

// Determine which collection and columns to display
$docs      ??= collect(); // fallback if variable not set
$role      ??= 'EMPLOYEE';
$searchTerm = request('all_search', '');

// Determine if the Action column should be shown
$showAction = false;
if(isset($showForwardedBy) && !$showForwardedBy) {
    // All Documents tab
    $showAction = in_array($role, ['DEPARTMENT-HEAD','CHIEF','ADMIN']);
} else {
    // Pending or Forwarded tabs: always show action column
    $showAction = true;
}

$showForwardedBy = $showForwardedBy ?? true; // optional override
@endphp

<div class="document-table">

    {{-- Table Headers --}}
    <div class="table-header">
        <div class="col">Doc No.</div>
        <div class="col">Document Name</div>
        <div class="col">Type</div>
        <div class="col">Current Section</div>
        @if($showForwardedBy)
            <div class="col">Forwarded by</div>
        @endif
        <div class="col">Original Holder</div>
        <div class="col">Date / Time</div>
        <div class="col">Status</div>
        @if($showAction)
            <div class="col">Action</div>
        @endif
    </div>

    {{-- Table Rows --}}
    @forelse ($docs as $doc)
        @php
            // If needed, get the last forwarded action
            $tracking = $doc->trackingHistory() ?? collect();
            $forwardAction = $tracking->where('action_type', 'FORWARDED')->sortByDesc('action_datetime')->first();
        @endphp

        <div class="table-row">
            <div class="col">{!! highlight($doc->document_number, $searchTerm) !!}</div>
            <div class="col">{!! highlight($doc->document_name, $searchTerm) !!}</div>
            <div class="col">{{ $doc->type->type_name ?? '-' }}</div>
            <div class="col">{{ $doc->currentSection->section_name ?? '-' }}</div>
            @if($showForwardedBy)
                <div class="col">{!! highlight($forwardAction?->user?->full_name ?? '-', $searchTerm) !!}</div>
            @endif
            <div class="col">{!! highlight($doc->createdBy->full_name ?? '-', $searchTerm) !!}</div>
            <div class="col">{{ $doc->updated_at->diffForHumans() ?? $doc->created_at ?? '-' }}</div>
            <div class="col status {{ strtolower($doc->status) }}">{{ ucfirst(strtolower($doc->status)) }}</div>
            @if($showAction)
                <div class="col action">
                    <a href="{{ route('documents.show', $doc) }}" class="btn btn-view">View</a>
                </div>
            @endif
        </div>

    @empty
        <div class="empty-table">No documents available.</div>
    @endforelse

</div>

{{-- Pagination --}}
<div class="pagination-container">
    {{ $docs->withQueryString()->links('components.custom-pagination') }}
</div>
