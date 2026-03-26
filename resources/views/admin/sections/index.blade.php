@extends('layouts.app')

@section('title', 'Sections')

@section('content')
<div class="sections-container">

    <!-- Header -->
    <div class="page-header">
        <h1>Sections</h1>
        <button id="addSectionBtn" class="btn btn-header"
            data-store-route="{{ route('sections.store') }}"
            data-search-route="{{ route('sections.search') }}"
            data-update-route="{{ route('sections.update', ':id') }}">
            + Add Section
        </button>
    </div>

    <!-- Search -->
    <div class="search-container">
        <input type="text" id="searchInput" class="search-input" placeholder="Search..." />

        <select id="departmentFilter" class="form-select">
            <option value="">All Departments</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->department_id }}">
                    {{ $dept->department_name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Sections Table -->
    <div id="sectionsTable">
        @include('admin.sections.search')
    </div>
</div>

@include('admin.sections.modal')

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    initLiveSearch({
        inputId: 'searchInput',
        containerId: 'sectionsTable',
        url: '/sections',
        extraParams: () => ({
            department_id: document.getElementById('departmentFilter')?.value
        })
    });
});
</script>
@endpush
