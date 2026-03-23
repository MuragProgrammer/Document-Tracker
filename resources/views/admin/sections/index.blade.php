@extends('layouts.app')

@section('title', 'Sections')

@section('content')
<div class="sections-container">
    <!-- Header -->
    <div class="page-header">
        <h1>Sections</h1>
        <button id="addSectionBtn" class="btn btn-header"
            data-store-route="{{ route('sections.store') }}"
            data-search-route="{{ route('sections.search') }}">
            + Add Section
        </button>
    </div>

    <!-- Search -->
    <div class="search-container">
        <input type="text" class="search-input" placeholder="Search..." />
    </div>

    <!-- Sections Table -->
    <table class="table-container">
        <thead>
            <tr>
                <th>Section Name</th>
                <th>Department</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sections as $section)
            <tr>
                <td>{{ $section->section_name }}</td>
                <td>{{ $section->department->department_name ?? '-' }}</td>
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
        <strong>Note:</strong> Editing is for misspellings or typos only. If a section has changed, add a new one.
    </p>
</div>

@include('admin.sections.modal')

@endsection
