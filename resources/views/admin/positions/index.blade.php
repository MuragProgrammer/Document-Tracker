@extends('layouts.app')

@section('title', 'Positions')

@section('content')
<div class="positions-container">

    <!-- Page Header -->
    <div class="page-header">
        <h1>Positions</h1>
        <button id="addPositionBtn" class="btn btn-header"
            data-store-route="{{ route('positions.store') }}"
            data-search-route="{{ route('positions.search') }}">
            + Add Position
        </button>
    </div>

    <!-- Search -->
    <div class="search-container">
        <input type="text" id="searchInput" class="search-input" placeholder="Search..." />
    </div>

    <!-- Table -->
    <div id="positionsTable">
        @include('admin.positions.search')
    </div>
</div>

@include('admin.positions.modal')
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    initLiveSearch({
        inputId: 'searchInput',
        containerId: 'positionsTable',
        url: '/positions'
    });
});
</script>
@endpush
