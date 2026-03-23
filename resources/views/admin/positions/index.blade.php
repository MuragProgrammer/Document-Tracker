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
        <input type="text" class="search-input" placeholder="Search..." />
    </div>

    <!-- Table -->
    <table class="table-container">
        <thead>
            <tr>
                <th>Position</th>
                <th>Plantilla Number</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($positions as $position)
            <tr>
                <td>{{ $position->position_title }}</td>
                <td>{{ $position->plantilla_number }}</td>
                <td>
                    <div class="status-toggle-wrapper">
                        <span class="status-label {{ $position->is_active ? 'enabled' : 'disabled' }}">
                            {{ $position->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </td>
                <td>
                    <button class="btn btn-edit editPositionBtn"
                        data-position-id="{{ $position->position_id }}"
                        data-title="{{ $position->position_title }}"
                        data-plantilla="{{ $position->plantilla_number }}"
                        data-active="{{ $position->is_active }}">
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
</div>

@include('admin.positions.modal')
@endsection
