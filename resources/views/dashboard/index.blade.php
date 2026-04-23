@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="dashboard-container">

    <!-- Header / Top Bar -->
    <div class="dashboard-header">
        <h1>Welcome, <span>{{ auth()->user()->full_name }}</span></h1>
    </div>

    <!-- Cards Row -->
    <div class="cards-container">
        <div class="dashboard-cards-row">

            <div class="card pending-receipt">
                <div class="card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="white" class="bi bi-clock-history" viewBox="0 0 16 16">
                    <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a7 7 0 0 0-.1-1.025l.985-.17q.1.58.116 1.17zm-.131 1.538q.05-.254.081-.51l.993.123a8 8 0 0 1-.23 1.155l-.964-.267q.069-.247.12-.501m-.952 2.379q.276-.436.486-.908l.914.405q-.24.54-.555 1.038zm-.964 1.205q.183-.183.35-.378l.758.653a8 8 0 0 1-.401.432z"/>
                    <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z"/>
                    <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5"/>
                    </svg>
                </div>
                <div class="card-info">
                    <div class="card-title">Pending Receipt</div>
                    <div class="card-value">{{ $cardCounts['pending_receipt'] }}</div>
                </div>
            </div>

            <div class="card pending-review">
                <div class="card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="white" class="bi bi-hourglass-split" viewBox="0 0 16 16">
                    <path d="M2.5 15a.5.5 0 1 1 0-1h1v-1a4.5 4.5 0 0 1 2.557-4.06c.29-.139.443-.377.443-.59v-.7c0-.213-.154-.451-.443-.59A4.5 4.5 0 0 1 3.5 3V2h-1a.5.5 0 0 1 0-1h11a.5.5 0 0 1 0 1h-1v1a4.5 4.5 0 0 1-2.557 4.06c-.29.139-.443.377-.443.59v.7c0 .213.154.451.443.59A4.5 4.5 0 0 1 12.5 13v1h1a.5.5 0 0 1 0 1zm2-13v1c0 .537.12 1.045.337 1.5h6.326c.216-.455.337-.963.337-1.5V2zm3 6.35c0 .701-.478 1.236-1.011 1.492A3.5 3.5 0 0 0 4.5 13s.866-1.299 3-1.48zm1 0v3.17c2.134.181 3 1.48 3 1.48a3.5 3.5 0 0 0-1.989-3.158C8.978 9.586 8.5 9.052 8.5 8.351z"/>
                    </svg>
                </div>
                <div class="card-info">
                    <div class="card-title">Under Review</div>
                    <div class="card-value">{{ $cardCounts['pending_review'] }}</div>
                </div>
            </div>

            <div class="card reopened">
                <div class="card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="white" class="bi bi-arrow-return-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M14.5 1.5a.5.5 0 0 1 .5.5v4.8a2.5 2.5 0 0 1-2.5 2.5H2.707l3.347 3.346a.5.5 0 0 1-.708.708l-4.2-4.2a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 8.3H12.5A1.5 1.5 0 0 0 14 6.8V2a.5.5 0 0 1 .5-.5"/>
                    </svg>
                </div>
                <div class="card-info">
                    <div class="card-title">Reopened</div>
                    <div class="card-value">{{ $cardCounts['reopened'] }}</div>
                </div>
            </div>

            <div class="card end">
                <div class="card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="white" class="bi bi-check2-square" viewBox="0 0 16 16">
                    <path d="M3 14.5A1.5 1.5 0 0 1 1.5 13V3A1.5 1.5 0 0 1 3 1.5h8a.5.5 0 0 1 0 1H3a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5h10a.5.5 0 0 0 .5-.5V8a.5.5 0 0 1 1 0v5a1.5 1.5 0 0 1-1.5 1.5z"/>
                    <path d="m8.354 10.354 7-7a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0"/>
                    </svg>
                </div>
                <div class="card-info">
                    <div class="card-title">End of Cycle</div>
                    <div class="card-value">{{ $cardCounts['end'] }}</div>
                </div>
            </div>

            <div class="card total-documents">
                <div class="card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="white" class="bi bi-file-earmark-check-fill" viewBox="0 0 16 16">
                    <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0M9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1m1.354 4.354-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708.708"/>
                    </svg>
                </div>
                <div class="card-info">
                    <div class="card-title">Total Documents</div>
                    <div class="card-value">{{ $cardCounts['total_documents'] }}</div>
                </div>
            </div>
        </div>
    </div>

<!-- Recent Documents Table -->
<div class="recent-documents-container">
    <div class="recent-documents-header">
        <div class="recent-documents-title">Recent Documents</div>
    </div>
    {{-- Search & Status Filter (Admin only) --}}
    @if(auth()->user()->role === 'ADMIN')
    <div class="filters column-filter">
        <input
            type="text"
            id="dashboardSearch"
            name="search"
            placeholder="Search by document number or name..."
            class="search-input"
            value="{{ request('search') }}"
        />
        <select id="statusFilter" name="status" class="status-select" onchange="this.form.submit()">
            <option value="">All status</option>
            <option value="PENDING" {{ request('status')=='PENDING'?'selected':'' }}>Pending Receipt</option>
            <option value="UNDER REVIEW" {{ request('status')=='UNDER REVIEW'?'selected':'' }}>Under Review</option>
            <option value="END OF CYCLE" {{ request('status')=='END OF CYCLE'?'selected':'' }}>End of cycle</option>
            <option value="REOPENED" {{ request('status')=='REOPENED'?'selected':'' }}>Reopend</option>
        </select>
        <button type="submit" hidden></button>
    </div>
    @endif

    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Doc No.</th>
                <th>Document Name</th>
                <th>Type</th>
                <th>Current Section</th>
                <th>Holder</th>
                <th>Date Created</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody id="dashboardTableBody">
            @include('dashboard.search', ['documents' => $documents, 'search' => request('search')])
        </tbody>
    </table>

    @if(
        auth()->user()->role === 'ADMIN' &&
        $documents instanceof \Illuminate\Pagination\AbstractPaginator
    )
    <div class="pagination-container">
        {{ $documents->links('components.custom-pagination') }}
    </div>
    @endif
</div>
@endsection


<script>
document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('dashboardSearch');
    const statusFilter = document.getElementById('statusFilter');

    function fetchDocuments() {
        let search = searchInput.value;
        let status = statusFilter.value;

        fetch(`{{ route('dashboard.search') }}?search=${search}&status=${status}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(data => {
            document.getElementById('dashboardTableBody').innerHTML = data;
        })
        .catch(err => console.error(err));
    }

    // debounce
    let timer;
    searchInput.addEventListener('keyup', function () {
        clearTimeout(timer);
        timer = setTimeout(fetchDocuments, 300);
    });

    statusFilter.addEventListener('change', fetchDocuments);

});
</script>
