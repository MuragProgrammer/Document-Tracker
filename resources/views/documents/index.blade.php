@extends('layouts.app')

@section('title', 'Documents')

@section('content')

<div class="document-tracker">

    {{-- Page Header --}}
    <div class="header">
        <h1>Documents</h1>
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
                    <input type="text" name="my_search" placeholder="Search my documents..." value="{{ request('my_search') }}" autocomplete="off">

                    {{-- Section Filter --}}
                    <select name="my_section_id">
                        @php
                            // Determine sections available in column 1
                            if (in_array($role, ['SECTION-HEAD', 'EMPLOYEE'])) {
                                $col1Sections = [$user->section]; // Only their section
                            } else{
                                $col1Sections = $sections->where('department_id', $user->section->department->department_id);
                            }
                        @endphp

                        @foreach($col1Sections as $section)
                            <option value="{{ $section->section_id }}"
                                {{ request('my_section_id', $user->section_id) == $section->section_id ? 'selected' : '' }}>
                                {{ $section->section_name }}
                            </option>
                        @endforeach
                    </select>

                    @if(!in_array($role, ['DEPARTMENT-HEAD', 'CHIEF', 'ADMIN']))
                        <input type="hidden" name="my_section_id" value="{{ $user->section_id }}">
                    @endif

                    <select name="my_status">
                        <option value="">All Status</option>
                        <option value="CREATED" {{ request('my_status')=='CREATED'?'selected':'' }}>Created</option>
                        <option value="PENDING" {{ request('my_status')=='PENDING'?'selected':'' }}>Pending</option>
                        <option value="FORWARDED" {{ request('my_status')=='FORWARDED'?'selected':'' }}>Forwarded</option>
                        <option value="UNDER REVIEW" {{ request('my_status')=='UNDER REVIEW'?'selected':'' }}>Under Review</option>
                        <option value="END OF CYCLE" {{ request('my_status')=='END OF CYCLE'?'selected':'' }}>End of Cycle</option>
                    </select>

                    <input type="hidden" name="col_1" value="{{ $col1 }}">
                    {{-- Preserve column 2 filters --}}
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
                    <input type="text" name="all_search" placeholder="Search documents..." value="{{ request('all_search') }}" autocomplete="off">

                    {{-- Section Filter --}}
                    <label>Section</label>
                    <select name="section_id">
                        @php
                            // Determine sections available in column 2
                            if (in_array($role, ['DEPARTMENT-HEAD', 'CHIEF', 'ADMIN'])) {
                                $col2Sections = $sections; // All sections
                            } else {
                                $col2Sections = [$user->section]; // Only their section
                            }
                        @endphp

                        @if(in_array($role, ['DEPARTMENT-HEAD', 'CHIEF', 'ADMIN']))
                            <option value="all" {{ request('section_id') == 'all' ? 'selected' : '' }}>All Sections</option>
                        @endif

                        @foreach($col2Sections as $section)
                            <option value="{{ $section->section_id }}"
                                {{ request('section_id', $user->section_id) == $section->section_id ? 'selected' : '' }}>
                                {{ $section->section_name }}
                            </option>
                        @endforeach
                    </select>

                    @if(!in_array($role, ['DEPARTMENT-HEAD', 'CHIEF', 'ADMIN']))
                        <input type="hidden" name="section_id" value="{{ $user->section_id }}">
                    @endif

                    <select name="all_status">
                        <option value="">All Status</option>
                        <option value="PENDING" {{ request('all_status')=='PENDING'?'selected':'' }}>Pending</option>
                        <option value="FORWARDED" {{ request('all_status')=='FORWARDED'?'selected':'' }}>Forwarded</option>
                        <option value="UNDER REVIEW" {{ request('my_status')=='UNDER REVIEW'?'selected':'' }}>Under Review</option>
                        <option value="END OF CYCLE" {{ request('all_status')=='END OF CYCLE'?'selected':'' }}>End of Cycle</option>
                    </select>

                    <input type="hidden" name="col_2" value="{{ $col2 }}">

                    {{-- preserve column 1 filters --}}
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
                <div class="document-table">
                    <div class="table-header">
                        <div class="col">Doc No.</div>
                        <div class="col">Document Name</div>
                        <div class="col">Type</div>
                        <div class="col">Current Section</div>
                        <div class="col">Forwarded by</div>
                        <div class="col">Original Holder</div>
                        <div class="col">Status</div>
                        <div class="col">Time</div>
                        <div class="col">Action</div>
                    </div>

                    @forelse ($pendingDocuments as $doc)
                        @php
                            $tracking = $doc->trackingHistory();
                            $forwardAction = $tracking
                                ->where('action_type', 'FORWARDED')
                                ->sortByDesc('action_datetime')
                                ->first();
                        @endphp
                        <div class="table-row">
                            <div class="col">{{ $doc->document_number }}</div>
                            <div class="col">{{ $doc->document_name }}</div>
                            <div class="col">{{ $doc->type->type_name ?? '-' }}</div>
                            <div class="col">{{ $doc->currentSection->section_name ?? '-' }}</div>
                            <div class="col">{{ $forwardAction?->user?->full_name ?? '-' }}</div>
                            <div class="col">{{ $doc->createdBy->full_name ?? '-' }}</div>
                            <div class="col status {{ strtolower($doc->status) }}">{{ ucfirst(strtolower($doc->status)) }}</div>
                            <div class="col">{{ $doc->updated_at->diffForHumans() }}</div>
                            <div class="col action">
                                <a href="{{ route('documents.show', $doc) }}" class="btn btn-view">View</a>
                            </div>
                        </div>
                    @empty
                        <div class="empty-table">No pending documents.</div>
                    @endforelse
                </div>
                <div class="pagination-container">
                    {{ $pendingDocuments->withQueryString()->links('components.custom-pagination') }}
                </div>
            </div>

            {{-- TAB: FORWARDED --}}
            <div class="tab-content {{ $col2=='forwarded'?'active':'' }}" id="forwarded">
                <div class="document-table">
                    <div class="table-header">
                        <div class="col">Doc No.</div>
                        <div class="col">Document Name</div>
                        <div class="col">Type</div>
                        <div class="col">Current Section</div>
                        <div class="col">Forwarded by</div>
                        <div class="col">Original Holder</div>
                        <div class="col">Status</div>
                        <div class="col">Action</div>
                    </div>

                    @forelse ($forwardedDocuments as $doc)
                        @php
                            $tracking = $doc->trackingHistory();
                            $forwardAction = $tracking
                                ->where('action_type', 'FORWARDED')
                                ->sortByDesc('action_datetime')
                                ->first();
                        @endphp
                        <div class="table-row">
                            <div class="col">{{ $doc->document_number }}</div>
                            <div class="col">{{ $doc->document_name }}</div>
                            <div class="col">{{ $doc->type->type_name ?? '-' }}</div>
                            <div class="col">{{ $doc->currentSection->section_name ?? '-' }}</div>
                            <div class="col">{{ $forwardAction?->user?->full_name ?? '-' }}</div>
                            <div class="col">{{ $doc->createdBy->full_name ?? '-' }}</div>
                            <div class="col status {{ strtolower($doc->status) }}">{{ ucfirst(strtolower($doc->status)) }}</div>
                            <div class="col action">
                                <a href="{{ route('documents.show', $doc) }}" class="btn btn-view">View</a>
                            </div>
                        </div>
                    @empty
                        <div class="empty-table">No forwarded documents.</div>
                    @endforelse
                </div>
                <div class="pagination-container">
                    {{ $forwardedDocuments->withQueryString()->links('components.custom-pagination') }}
                </div>
            </div>

            {{-- TAB: ALL --}}
            <div class="tab-content {{ $col2=='all'?'active':'' }}" id="all">
                <div class="document-table">
                    <div class="table-header">
                        <div class="col">Doc No.</div>
                        <div class="col">Document Name</div>
                        <div class="col">Type</div>
                        <div class="col">Current Section</div>
                        <div class="col">Original Holder</div>
                        <div class="col">Date </div>
                        <div class="col">Status</div>
                         @if(in_array($role, ['DEPARTMENT-HEAD', 'CHIEF', 'ADMIN']))
                        <div class="col">Action</div>
                        @endif
                    </div>

                    @forelse ($allDocuments as $doc)
                        <div class="table-row">
                            <div class="col">{{ $doc->document_number }}</div>
                            <div class="col">{{ $doc->document_name }}</div>
                            <div class="col">{{ $doc->type->type_name ?? '-' }}</div>
                            <div class="col">{{ $doc->currentSection->section_name ?? '-' }}</div>
                            <div class="col">{{ $doc->createdBy->full_name ?? '-' }}</div>
                            <div class="col">{{ $doc->created_at ?? '-' }}</div>
                            <div class="col status {{ strtolower($doc->status) }}">{{ ucfirst(strtolower($doc->status)) }}</div>
                             @if(in_array($role, ['DEPARTMENT-HEAD', 'CHIEF', 'ADMIN']))
                            <div class="col action">
                                <a href="{{ route('documents.show', $doc) }}" class="btn btn-view">View</a>
                            </div>
                            @endif
                        </div>
                    @empty
                        <div class="empty-table">No documents available.</div>
                    @endforelse
                </div>
                <div class="pagination-container">
                    {{ $allDocuments->withQueryString()->links('components.custom-pagination') }}
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

            // Navigate to URL to trigger Laravel reload and preserve pagination
            window.location.href = btn.href;
        });
    });

    // -------------------------
    // Auto-submit form on status or section change
    // -------------------------
    document.querySelectorAll('select[name$="_status"], select[name$="_section_id"], select[name="section_id"]').forEach(select => {
        select.addEventListener('change', function() {
            const form = select.closest('form');
            if(form) form.submit();
        });
    });

});
</script>
@endpush
