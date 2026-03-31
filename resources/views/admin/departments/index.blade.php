@extends('layouts.app')

@section('title', 'Departments')

@section('content')

    <div class="department-container">

        <!-- Header -->
        <div class="page-header">
            <h1>Departments</h1>
            <button id="addDeptBtn" class="btn btn-header" data-search-route="{{ route('departments.search') }}">+ Add
                Department</button>
        </div>

        <!-- Departments Table -->
        <table class="table-container">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($departments as $department)
                    <tr>
                        <td>{{ $department->department_name }}</td>
                        <td>{{ $department->department_code }}</td>
                        <td>
                            <div class="status-toggle-wrapper">
                                <span class="status-label {{ $department->is_active ? 'enabled' : 'disabled' }}">
                                    {{ $department->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </td>
                        <td>{{ $department->created_at->format('Y-m-d') }}</td>
                        <td>
                            <button class="btn btn-edit editDeptBtn" data-department-id="{{ $department->department_id }}"
                                data-name="{{ $department->department_name }}"
                                data-code="{{ $department->department_code }}" data-active="{{ $department->is_active }}">
                                Edit
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p class="note">
            <strong>Note:</strong> Editing is for misspellings or typos only. If a department has changed, add a new one.
        </p>

    </div>

    @include('admin.departments.modal')

@endsection
