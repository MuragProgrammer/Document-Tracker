@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="reports-grid py-4">

    <!-- =========================
        HEADER + FILTER
    ========================= -->
    <div class="grid-header">
        <div class="reports-header">
            <h2 class="fw-bold mb-0">Document Status Summary Report</h2>
            <div class="filter-grid mb-4">
                <form method="GET" action="{{ route('reports.index') }}" id="filterForm">
                    <!-- Start Date -->
                    <div class="grid-item start-date">
                        <label>Start Date</label>
                        <input type="date" name="from" value="{{ request('from') }}">
                    </div>

                    <!-- End Date -->
                    <div class="grid-item end-date">
                        <label>End Date</label>
                        <input type="date" name="to" value="{{ request('to') }}">
                    </div>

                    <!-- Status -->
                    <div class="grid-item r-status">
                        <label>Status</label>
                        <select name="status">
                            <option value="">All</option>
                            @foreach(['PENDING','UNDER REVIEW','END OF CYCLE','REOPENED'] as $status)
                                <option value="{{ $status }}" @selected(request('status') == $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Document Type -->
                    <div class="grid-item doc-type">
                        <label>Document Type</label>
                        <select name="type">
                            <option value="">All</option>
                            @foreach($documentTypes as $type)
                                <option value="{{ $type->type_id }}" @selected(request('type') == $type->type_id)>{{ $type->type_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Department / Section Toggle -->
                    <div class="grid-item dept-sec-toggle">
                        <label>Filter By</label>
                        <select name="filter_by" id="filterBySelect"
                            @if(!$isAdmin && !$isChief && !$isDivHead) disabled @endif>

                            @if($isAdmin || $isChief)
                                <option value="department" @selected(request('filter_by')=='department')>Department</option>
                                <option value="section" @selected(request('filter_by')=='section')>Section</option>

                            @elseif($isDivHead)
                                <option value="department" @selected(request('filter_by','department')=='department')>Department</option>
                                <option value="section" @selected(request('filter_by')=='section')>Section</option>

                            @else
                                <option value="section" selected>Your Section</option>
                            @endif

                        </select>
                    </div>

                    <!-- Department / Section Select -->
                    <div class="grid-item dept-sec-select">
                        <label>&nbsp;</label>
                        <select name="dept_or_sec"
                            id="deptOrSecSelect"
                            @if(!$isAdmin && !$isChief && !$isDivHead) disabled @endif>

                            {{-- =========================
                                ADMIN / CHIEF
                            ========================= --}}
                            @if($isAdmin || $isChief)

                                @if(request('filter_by','department')=='department')
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->department_id }}"
                                            @selected(request('dept_or_sec')==$dept->department_id)>
                                            {{ $dept->department_name }}
                                        </option>
                                    @endforeach
                                @else
                                    @foreach($sections as $sec)
                                        <option value="{{ $sec->section_id }}"
                                            @selected(request('dept_or_sec')==$sec->section_id)>
                                            {{ $sec->section_name }}
                                        </option>
                                    @endforeach
                                @endif


                            {{-- =========================
                                DIVISION HEAD
                            ========================= --}}
                            @elseif($isDivHead)

                                @php
                                    $userDeptId = auth()->user()->section->department_id;
                                @endphp

                                {{-- DEFAULT: DEPARTMENT --}}
                                @if(request('filter_by','department') == 'department')

                                    <option value="{{ $userDeptId }}" selected>
                                        {{ auth()->user()->section->department->department_name }}
                                    </option>

                                {{-- SWITCHED TO SECTION --}}
                                @else

                                    @foreach($sections->where('department_id', $userDeptId) as $sec)
                                        <option value="{{ $sec->section_id }}"
                                            @selected(request('dept_or_sec')==$sec->section_id)>
                                            {{ $sec->section_name }}
                                        </option>
                                    @endforeach

                                @endif


                            {{-- =========================
                                NORMAL USER
                            ========================= --}}
                            @else

                                <option value="{{ auth()->user()->section_id }}" selected>
                                    {{ auth()->user()->section->section_name }}
                                </option>

                            @endif

                        </select>
                    </div>

                    <!-- Clear Filter -->
                    <div class="grid-item clear-filter">
                        <a href="{{ route('reports.index') }}" class="btn btn-success w-full mt-4">Clear Filter</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- =========================
        KPI CARDS
    ========================= -->
    <div class="grid-kpi">
        <div class="cards-container mb-4">
            <div class="cards-row">
                @php
                    $cards = [
                        ['title'=>'Pending','value'=>$cardCounts['pending_receipt'],'class'=>'pending-receipt','icon'=>'bi-clock-history'],
                        ['title'=>'Under Review','value'=>$cardCounts['pending_review'],'class'=>'pending-review','icon'=>'bi-hourglass-split'],
                        ['title'=>'Completed','value'=>$cardCounts['completed'],'class'=>'completed','icon'=>'bi-check2-square'],
                        ['title'=>'Reopened','value'=>$cardCounts['returned'],'class'=>'returned','icon'=>'bi-arrow-return-left'],
                    ];
                @endphp

                @foreach($cards as $card)
                    <div class="card {{ $card['class'] }}" data-status="{{ $card['title'] }}">
                        <div class="card-icon">
                            <i class="bi {{ $card['icon'] }}"></i>
                        </div>
                        <div class="card-info">
                            <div class="card-title">{{ $card['title'] }}</div>
                            <div class="card-value">{{ $card['value'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>



    <!-- =========================
        CHARTS
    ========================= -->
    <div class="grid-donut">
        <canvas id="donutChart"></canvas>
    </div>


    <!-- STACKED -->
    <div class="grid-stacked">
        <canvas id="sectionStackedChart"></canvas>
    </div>

    <!-- LINE -->
    <div class="grid-line">
        <div class="chart-header">
            <h6>Documents Created Over Time</h6>
            <select id="trendPeriod" class="form-select form-select-sm">
                <option value="day" {{ $period=='day' ? 'selected' : '' }}>Daily</option>
                <option value="week" {{ $period=='week' ? 'selected' : '' }}>Weekly</option>
                <option value="month" {{ $period=='month' ? 'selected' : '' }}>Monthly</option>
                <option value="year" {{ $period=='year' ? 'selected' : '' }}>Yearly</option>
            </select>
        </div>
        <canvas id="lineChart"></canvas>
    </div>

    <!-- =========================
        SUMMARY TABLE
    ========================= -->
    <div class="grid-table">
        <div class="reportx-table-wrapper">
            <div class="reportx-card">
                <div class="reportx-card-header">Document Status Summary</div>
                <div class="table-responsive">
                    <table class="table reportx-summary-table text-center">
                        <thead>
                            <tr>
                                <th>Sections</th>
                                @foreach ($labels as $label)<th>{{ $label }}</th>@endforeach
                                <th>Total Documents</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sectionSummary ?? [] as $section => $data)
                                <tr>
                                    <td>{{ $section }}</td>
                                    @foreach ($labels as $label)<td>{{ $data[$label] ?? 0 }}</td>@endforeach
                                    <td>{{ array_sum($data) }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td><strong>Total</strong></td>
                                @foreach ($counts as $count)<td><strong>{{ $count }}</strong></td>@endforeach
                                <td><strong>{{ array_sum($counts->toArray()) }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- =========================
        EXPORT BUTTONS
    ========================= -->
    <div class="grid-export">
        <div class="export-wrapper mb-4">
            <button id="pdfBtn" class="btn btn-danger me-2">
                <span class="btn-text">Export PDF</span>
                <span class="spinner"></span>
            </button>
            <button id="csvBtn" class="btn btn-success">
                <span class="btn-text">Export Excel</span>
                <span class="spinner"></span>
            </button>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // =============================
    // REPORT DATA (for charts)
    // =============================
    window.REPORTS = {
        labels: @json($labels),
        counts: @json($counts),
        sectionNames: @json($sectionNames),
        sectionChartData: @json($sectionChartData),
        trendLabels: @json($trendLabels),
        trendCounts: @json($trendCounts)
    };

    // =============================
    // ELEMENTS
    // =============================
    const filterForm = document.getElementById('filterForm');
    const filterBySelect = document.getElementById('filterBySelect');
    const deptOrSecSelect = document.getElementById('deptOrSecSelect');
    const trendPeriod = document.getElementById('trendPeriod');
    const pdfBtn = document.getElementById('pdfBtn');
    const csvBtn = document.getElementById('csvBtn');

    if (!filterForm) return;

    // =============================
    // AUTO SUBMIT ON FILTER CHANGE
    // =============================
    filterForm.querySelectorAll('input, select').forEach(element => {

        element.addEventListener('change', function () {

            // Prevent unnecessary submit if no value selected
            if (this.name === 'dept_or_sec' && !this.value) {
                return;
            }

            filterForm.submit();
        });

    });

    // =============================
    // TREND PERIOD CHANGE
    // =============================
    if (trendPeriod) {
        trendPeriod.addEventListener('change', function () {
            const params = new URLSearchParams(window.location.search);
            params.set('period', this.value);
            window.location.search = params.toString();
        });
    }

    // =============================
    // EXPORT FUNCTIONS
    // =============================
    function exportPDF() {
        const params = new URLSearchParams(window.location.search);
        window.location.href = "{{ route('reports.export.pdf') }}?" + params.toString();
    }

    function exportCSV() {
        const params = new URLSearchParams(window.location.search);
        window.location.href = "{{ route('reports.export.csv') }}?" + params.toString();
    }

    if (pdfBtn) pdfBtn.addEventListener('click', exportPDF);
    if (csvBtn) csvBtn.addEventListener('click', exportCSV);

});
</script>
@endsection
