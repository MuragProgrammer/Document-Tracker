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
    <input type="text" id="searchInput" placeholder="Search by name or username..." class="search-input">
</div>

<!-- User Table -->
<div id="userTable">
    @include('admin.users.search', ['users' => $users, 'search' => ''])
</div>

<!-- Include the user modal -->
@include('admin.users.modal')
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    initLiveSearch({
        inputId: 'searchInput',
        containerId: 'userTable', // ✅ match the div ID
        url: '/users',
        extraParams: () => ({})
    });
});
</script>
@endpush
