@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="page-header">
    <h1>User Management</h1>
    <button id="addUserBtn" class="btn btn-header" data-store-route="{{ route('users.store') }}">
        + Add User
    </button>
</div>

<!-- Search -->
<div class="search-container">
    <input type="text" placeholder="Search..." class="search-input">
</div>

<table class="table-container">
    <thead>
        <tr>
            <th>Full Name</th>
            <th>Username</th>
            <th>Section</th>
            <th>Position</th>
            <th>Role</th>
            <th>Status</th>
            <th width="160">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr>
            <td>{{ $user->full_name }}</td>
            <td>{{ $user->username }}</td>
            <td>{{ $user->section->section_name ?? '-' }}</td>
            <td>{{ $user->position->position_title ?? '-' }}</td>
            <td>{{ $user->role }}</td>
            <td>
                    <div class="status-toggle-wrapper">
                        <span class="status-label {{ $user->is_active ? 'enabled' : 'disabled' }}">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
            </td>
            <td>
                <button
                    class="btn btn-edit editUserBtn"
                    data-user-id="{{ $user->user_id }}"
                    data-full-name="{{ $user->full_name }}"
                    data-username="{{ $user->username }}"
                    data-section-id="{{ $user->section_id }}"
                    data-department-name="{{ $user->section->department->department_name ?? '' }}"
                    data-position-id="{{ $user->position_id }}"
                    data-role="{{ $user->role }}"
                    data-active="{{ $user->is_active }}">
                    Edit
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Include the user modal -->
@include('admin.users.modal')
@endsection
