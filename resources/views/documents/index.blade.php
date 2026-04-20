@extends('layouts.app')

@section('title', 'Documents')

@section('content')

<div class="document-tracker">

    {{-- Page Header --}}
    <div class="header">
        <h1>Documents</h1>

        {{-- Section Handling --}}
        @if(in_array($role, ['SECTION-HEAD', 'EMPLOYEE']))

            {{-- ✅ Show note instead of dropdown --}}
            <p class="note">
                <strong>Note:</strong> Due to confidentiality,
                you can only view documents from your section
                <strong>{{ $user->section->section_name }}</strong>.
            </p>
        @endif

        <a href="{{ route('documents.create') }}" class="btn btn-header">
            <span class="icon">+</span>
            <span>Add Document</span>
        </a>
    </div>

    <div class="two-column-layout">

        {{-- =====================================================
            COLUMN 1 : MY DOCUMENTS
        ====================================================== --}}
        @php $col1 = request('col_1', 'handled'); @endphp
        <div class="document-column">

            {{-- Filters --}}
            <div class="filters column-filter">
                <form method="GET" action="{{ route('documents.index') }}">

                    {{-- Search --}}
                    <input type="text" name="my_search"
                        placeholder="Search my documents..."
                        value="{{ request('my_search') }}"
                        autocomplete="off">

                    {{-- Section Handling --}}
                    @if(in_array($role, ['SECTION-HEAD', 'EMPLOYEE']))
                        {{-- Keep backend filter --}}
                        <input type="hidden" name="my_section_id" value="{{ $user->section_id }}">

                    @else
                        {{-- ✅ Show dropdown for higher roles --}}
                        <select name="my_section_id">
                            @php
                                $col1Sections = $sections->where(
                                    'department_id',
                                    $user->section->department->department_id
                                );
                            @endphp

                            @foreach($col1Sections as $section)
                                <option value="{{ $section->section_id }}"
                                    {{ request('my_section_id', $user->section_id) == $section->section_id ? 'selected' : '' }}>
                                    {{ $section->section_name }}
                                </option>
                            @endforeach
                        </select>
                    @endif

                    {{-- Status Filter --}}
                    <select name="my_status">
                        <option value="">All Status</option>
                        <option value="CREATED" {{ request('my_status')=='CREATED'?'selected':'' }}>Created</option>
                        <option value="PENDING" {{ request('my_status')=='PENDING'?'selected':'' }}>Pending</option>
                        <option value="FORWARDED" {{ request('my_status')=='FORWARDED'?'selected':'' }}>Forwarded</option>
                        <option value="UNDER REVIEW" {{ request('my_status')=='UNDER REVIEW'?'selected':'' }}>Under Review</option>
                        <option value="END OF CYCLE" {{ request('my_status')=='END OF CYCLE'?'selected':'' }}>End of Cycle</option>
                    </select>

                    {{-- Preserve states --}}
                    <input type="hidden" name="col_1" value="{{ $col1 }}">
                    <input type="hidden" name="all_search" value="{{ request('all_search') }}">
                    <input type="hidden" name="all_status" value="{{ request('all_status') }}">
                    <input type="hidden" name="col_2" value="{{ request('col_2') }}">

                    <button type="submit" hidden></button>
                </form>
            </div>
            {{-- Tabs --}}
            <div class="tabs">
                <a href="{{ route('documents.index', array_merge(request()->all(), ['col_1'=>'handled'])) }}"
                   class="tab-btn {{ $col1=='handled'?'active':'' }}">Handled Documents</a>
                <a href="{{ route('documents.index', array_merge(request()->all(), ['col_1'=>'created'])) }}"
                   class="tab-btn {{ $col1=='created'?'active':'' }}">Created  Documents</a>
            </div>

            {{-- TAB: HANDLED --}}
            <div class="tab-content {{ $col1=='handled'?'active':'' }}" id="handled">
                <div class="document-table">
                    <div class="table-header">
                        <div class="col">Doc No.</div>
                        <div class="col">Document Name</div>
                        <div class="col">Type</div>
                        <div class="col">Current Section</div>
                        <div class="col">Original Holder</div>
                        <div class="col">Date </div>
                        <div class="col">Status</div>
                        <div class="col">Action</div>
                    </div>

                    @forelse ($myDocuments as $doc)
                        <div class="table-row">
                            <div class="col">{{ $doc->document_number }}</div>
                            <div class="col">{{ $doc->document_name }}</div>
                            <div class="col">{{ $doc->type->type_name ?? '-' }}</div>
                            <div class="col">{{ $doc->currentSection->section_name ?? '-' }}</div>
                            <div class="col">{{ $doc->createdBy->first_name ?? '-' }}</div>
                            <div class="col">{{ $doc->created_at ?? '-' }}</div>
                            <div class="col status {{ strtolower($doc->status) }}">{{ ucfirst(strtolower($doc->status)) }}</div>
                            <div class="col action">
                                <a href="{{ route('documents.show', $doc) }}" class="btn btn-view">View</a>
                            </div>
                        </div>
                    @empty
                        <div class="empty-table">No documents currently handled.</div>
                    @endforelse
                </div>
                <div class="pagination-container">
                    {{ $myDocuments->withQueryString()->links('components.custom-pagination') }}
                </div>
            </div>

            {{-- TAB: CREATED --}}
            <div class="tab-content {{ $col1=='created'?'active':'' }}" id="created">
                <div class="document-table">
                    <div class="table-header">
                        <div class="col">Doc No.</div>
                        <div class="col">Document Name</div>
                        <div class="col">Type</div>
                        <div class="col">Current Section</div>
                        <div class="col">Created By</div>
                        <div class="col">Date </div>
                        <div class="col">Status</div>
                        <div class="col">Action</div>
                    </div>

                    @forelse ($myCreatedDocuments as $doc)
                        <div class="table-row">
                            <div class="col">{{ $doc->document_number }}</div>
                            <div class="col">{{ $doc->document_name }}</div>
                            <div class="col">{{ $doc->type->type_name ?? '-' }}</div>
                            <div class="col">{{ $doc->currentSection->section_name ?? '-' }}</div>
                            <div class="col">{{ $doc->createdBy->full_name ?? '-' }}</div>
                            <div class="col">{{ $doc->created_at ?? '-' }}</div>
                            <div class="col status {{ strtolower($doc->status) }}">{{ ucfirst(strtolower($doc->status)) }}</div>
                            <div class="col action">
                                <a href="{{ route('documents.show', $doc) }}" class="btn btn-view">View</a>
                            </div>
                        </div>
                    @empty
                        <div class="empty-table">No  documents.</div>
                    @endforelse
                </div>
                <div class="pagination-container">
                    {{ $myCreatedDocuments->withQueryString()->links('components.custom-pagination') }}
                </div>
            </div>

        </div> {{-- End Column 1 --}}

        {{-- =====================================================
            COLUMN 2 : FORWARDED / ALL DOCUMENTS
        ====================================================== --}}
        @php $col2 = request('col_2', 'forwarded'); @endphp
        <div class="document-column">

            {{-- Filters --}}
            <div class="filters column-filter">
                <form method="GET" action="{{ route('documents.index') }}">

                    {{-- Search --}}
                    <input type="text"
                        id="docSearchInput"
                        name="all_search"
                        placeholder="Search documents..."
                        value="{{ request('all_search') }}"
                        autocomplete="off">

                    {{-- Section Handling --}}
                    @if(in_array($role, ['SECTION-HEAD', 'EMPLOYEE']))
                        {{-- Keep backend filter --}}
                        <input type="hidden" name="section_id" value="{{ $user->section_id }}">
                    @else

                        {{-- ✅ Full section filter for higher roles --}}
                        <label>Section</label>
                        <select name="section_id">

                            {{-- Default ALL option --}}
                            <option value="all" {{ request('section_id', 'all') == 'all' ? 'selected' : '' }}>
                                All Sections (System-wide)
                            </option>

                            @foreach($sections as $section)
                                <option value="{{ $section->section_id }}"
                                    {{ request('section_id') == $section->section_id ? 'selected' : '' }}>
                                    {{ $section->section_name }}
                                </option>
                            @endforeach
                        </select>

                    @endif

                    {{-- Status Filter --}}
                    <select name="all_status">
                        <option value="">All Status</option>
                        <option value="PENDING" {{ request('all_status')=='PENDING'?'selected':'' }}>Pending</option>
                        <option value="FORWARDED" {{ request('all_status')=='FORWARDED'?'selected':'' }}>Forwarded</option>
                        <option value="UNDER REVIEW" {{ request('all_status')=='UNDER REVIEW'?'selected':'' }}>Under Review</option>
                        <option value="END OF CYCLE" {{ request('all_status')=='END OF CYCLE'?'selected':'' }}>End of Cycle</option>
                    </select>

                    {{-- Preserve states --}}
                    <input type="hidden" name="col_2" value="{{ $col2 }}">
                    <input type="hidden" name="my_search" value="{{ request('my_search') }}">
                    <input type="hidden" name="my_status" value="{{ request('my_status') }}">
                    <input type="hidden" name="col_1" value="{{ request('col_1') }}">

                    <button type="submit" hidden></button>
                </form>
            </div>

            {{-- Tabs --}}
            <div class="tabs">
                <a href="{{ route('documents.index', array_merge(request()->all(), ['col_2'=>'pending'])) }}"
                   class="tab-btn {{ $col2=='pending'?'active':'' }}">Pending Documents</a>
                <a href="{{ route('documents.index', array_merge(request()->all(), ['col_2'=>'forwarded'])) }}"
                   class="tab-btn {{ $col2=='forwarded'?'active':'' }}">Forwarded Documents</a>
                <a href="{{ route('documents.index', array_merge(request()->all(), ['col_2'=>'all'])) }}"
                   class="tab-btn {{ $col2=='all'?'active':'' }}">All Documents</a>
            </div>

            {{-- TAB: PENDING --}}
            <div class="tab-content {{ $col2=='pending'?'active':'' }}" id="pending">
                <div id="pendingTable">
                    @include('documents.search', [
                        'docs' => $pendingDocuments,
                        'showForwardedBy' => true
                    ])
                </div>
            </div>

            {{-- TAB: FORWARDED --}}
            <div class="tab-content {{ $col2=='forwarded'?'active':'' }}" id="forwarded">
                <div id="forwardedTable">
                    @include('documents.search', [
                        'docs' => $forwardedDocuments,
                        'showForwardedBy' => true
                    ])
                </div>
            </div>

            {{-- TAB: ALL --}}
            <div class="tab-content {{ $col2=='all'?'active':'' }}" id="all">
                <div id="allTable">
                    @include('documents.search', [
                        'docs' => $allDocuments,
                        'showForwardedBy' => false
                    ])
                </div>
            </div>

        </div> {{-- End Column 2 --}}

    </div> {{-- End two-column-layout --}}
</div> {{-- End document-tracker --}}

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // -------------------------
    // Tab switching
    // -------------------------
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            const column = btn.closest('.document-column');
            const tab = btn.getAttribute('href').split('#')[1];

            column.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            column.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

            btn.classList.add('active');
            const content = column.querySelector(`#${tab}`);
            if(content) content.classList.add('active');

            // Trigger full page reload to preserve Laravel pagination
            window.location.href = btn.href;
        });
    });

    // -------------------------
    // Auto-submit form on status or section change
    // -------------------------
    document.querySelectorAll('select[name$="_status"], select[name$="_section_id"], select[name="section_id"]')
        .forEach(select => {
            select.addEventListener('change', function () {
                const form = select.closest('form');
                if(form) form.submit();
            });
        });

    // -------------------------
    // Live search for multiple tables
    // -------------------------
    document.querySelectorAll('input[name$="_search"]').forEach(input => {
        let timer;
        input.addEventListener('keyup', function () {
            clearTimeout(timer);

            timer = setTimeout(() => {
                const query = input.value.trim();
                const column = input.closest('.document-column');
                const activeTab = column.querySelector('.tab-content.active');

                if(!activeTab) return;

                const tableWrapper = activeTab.querySelector('div[id$="Table"]');
                if(!tableWrapper) return;

                const tableId = tableWrapper.id; // pendingTable, forwardedTable, allTable
                const col2 = tableId.replace('Table',''); // pending, forwarded, all

                // Prepare parameters
                const form = input.closest('form');
                const params = new URLSearchParams(new FormData(form));
                params.set(input.name, query);
                params.set('col_2', col2);

                fetch(`{{ route('documents.index') }}?${params.toString()}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.text())
                .then(html => {
                    tableWrapper.innerHTML = html;
                });
            }, 300); // debounce
        });
    });
});
</script>
@endpush
