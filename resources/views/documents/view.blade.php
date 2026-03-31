@extends('layouts.app')

@section('content')
<div class="document-view-container">

    {{-- Document Info Box --}}
    <div class="document-info-box">
        <h2>Document Details</h2>

        <div class="grid">
            <div class="doc-att">

                @php
                    $preview = $document->attachments->first();
                    $attachments = $document->attachments->slice(1); // remove preview from list
                @endphp

{{-- PREVIEW (BIG) --}}
<div class="preview-card">
    @if ($preview)
        @if (str_starts_with($preview->file_type, 'image'))
            <img
                src="{{ asset('storage/' . $preview->file_path) }}"
                alt="{{ $preview->file_original_name }}"
            >
        @elseif ($preview->file_type === 'application/pdf')
            <div class="pdf-wrapper scrollable">
                <embed
                    src="{{ asset('storage/' . $preview->file_path) }}#page=1&zoom=FitH&toolbar=0"
                    type="application/pdf"
                />
                <!-- Invisible overlay link -->
                <a href="{{ asset('storage/' . $preview->file_path) }}" target="_blank" class="pdf-overlay-link"></a>
            </div>
        @else
            <div class="file-card big">FILE</div>
        @endif
    @endif
</div>

                {{-- ATTACHMENTS GRID --}}
                @forelse ($attachments as $attachment)
                    <a
                        href="{{ asset('storage/' . $attachment->file_path) }}"
                        target="_blank"
                        class="gallery-card"
                    >
                        @if (str_starts_with($attachment->file_type, 'image'))
                            <img src="{{ asset('storage/' . $attachment->file_path) }}">
                        @elseif ($attachment->file_type === 'application/pdf')
                            <div class="pdf-preview">
                                <embed
                                    src="{{ asset('storage/' . $attachment->file_path) }}#page=1&zoom=FitH&toolbar=0"
                                    type="application/pdf"
                                />
                            </div>
                        @else
                            <div class="file-card">📁</div>
                        @endif
                    </a>
                @empty
                    <p class="no-files">No files attached</p>
                @endforelse

            </div>
            <div class="doc-no">
                <p class="font-medium">Document Number:</p>
                <p>{{ $document->document_number }}</p>
            </div>
            <div class="doc-name">
                <p class="font-medium">Document Name:</p>
                <p>{{ $document->document_name }}</p>
            </div>
            <div class="doc-type">
                <p class="font-medium">Document Type:</p>
                <p>{{ $document->type->type_name ?? '--' }}</p>
            </div>
            <div class="org-section">
                <p class="font-medium">Originating Section:</p>
                <p>{{ $document->originatingSection->section_name ?? '--' }}</p>
            </div>
            <div class="cur-section">
                <p class="font-medium">Current Section:</p>
                <p>{{ $document->currentSection->section_name ?? '--' }}</p>
            </div>
            <div class="doc-status">
                <p class="font-medium">Status:</p>
                <div class="col status {{ strtolower($document->status) }}">{{ ucfirst(strtolower($document->status)) }}</div>
            </div>
            <div class="doc-date">
                <p class="font-medium">Date Created:</p>
                <p>{{ $document->created_at->format('M d, Y h:i A') }}</p>
            </div>
            <div class="created">
                <p class="font-medium">Created By:</p>
                <p>{{ $document->createdBy->full_name ?? '--' }}</p>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="document-action-buttons">
        {{-- END OF CYCLE → Reopen only --}}
        @if ($document->status === 'END OF CYCLE')
            <form method="POST" action="{{ route('documents.action', $document) }}" style="display:inline">
                @csrf
                <input type="hidden" name="action_type" value="Reopen">
                <button type="submit" class="reopen">Reopen</button>
            </form>

        {{-- FORWARDED or PENDING → Accept only (same section) --}}
        @elseif (
            in_array($document->status, ['FORWARDED', 'PENDING']) &&
            $document->current_section_id === auth()->user()->section_id
        )
            <form method="POST" action="{{ route('documents.action', $document) }}" style="display:inline">
                @csrf
                <input type="hidden" name="action_type" value="Accept">
                <button type="submit" class="accept">Accept</button>
            </form>

        {{-- CREATED / UNDER REVIEW → Forward + End Cycle (current holder only) --}}
        @elseif (
            in_array($document->status, ['CREATED', 'UNDER REVIEW', 'REOPENED']) &&
            $document->current_section_id === auth()->user()->section_id
        )
            {{-- Forward Button triggers modal for section selection --}}
            <button type="button" class="forward modal-open" data-action="Forward">Forward</button>

            {{-- End Cycle Button triggers modal for remarks --}}
            <button type="button" class="end-cycle modal-open" data-action="End Cycle">End Cycle</button>

        @endif

        {{-- Delete Button only for CREATED status --}}
        @if ($document->status === 'CREATED')
            <form method="POST"
                action="{{ route('documents.destroy', $document->doc_id) }}"
                style="display:inline"
                onsubmit="return confirm('Are you sure you want to delete this document?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="delete">Delete</button>
            </form>
        @endif

    </div>

    {{-- Tracking History --}}
    <div class="tracking-history">
        <h2 class="tracking-title">Tracking History</h2>
        <a href="{{ route('documents.exportTrackingPdf', $document->doc_id) }}"
        class="btn btn-sm btn-primary" target="_blank">
            Export PDF
        </a>
        
        <table class="tracking-table">
        <thead>
            <tr>
                <th>Activity</th>
                <th>Date & Time</th>
                <th>Remarks</th>
            </tr>
        </thead>

        <tbody>
            @forelse($document->trackingHistory() as $track)
                @php
                    $toSection   = $track->section->section_name ?? '-';
                    $userName    = $track->user->full_name ?? 'Unknown';
                    $action      = strtolower($track->action_type ?? '');
                    $activityText = '';

                    switch($action) {
                        case 'created':
                            $activityText = "Created by <strong>{$userName}</strong>";
                            break;
                        case 'forwarded':
                            $activityText = "Forwarded to <strong>{$toSection}</strong><br>by <strong>{$userName}</strong>";
                            break;
                        case 'received':
                            $activityText = "Received by <strong>{$userName}</strong><br>in <strong>{$toSection}</strong>";
                            break;
                        case 'ended':
                            $activityText = "Ended by <strong>{$userName}</strong><br>in <strong>{$toSection}</strong>";
                            break;
                        case 'reopened':
                            $activityText = "Reopened by <strong>{$userName}</strong><br>in <strong>{$toSection}</strong>";
                            break;
                        default:
                            $activityText = "{$action} by <strong>{$userName}</strong>";
                    }
                @endphp
                <tr>
                    <td>{!! $activityText !!}</td>
                    <td>{{ $track->action_datetime ?? '--' }}</td>
                    <td>{{ $track->remarks ?? '--' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="empty-row">No tracking history available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

{{-- Modal --}}
<div id="actionModal" class="modal-overlay hidden">
    <div class="modal-content">
        <h2 class="modal-title">Action</h2>

        <form id="actionForm" method="POST" action="{{ route('documents.action', $document->doc_id) }}">
            @csrf
            <input type="hidden" name="action_type" id="action_type">
            <input type="hidden" name="section_id" id="modal_section_id">

            <div id="sectionContainer" style="display: none;">
                <label class="modal-label">Select Section</label>
                <select class="modal-input" id="select_section" name="section_id">
                    <option value="">Select Section</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->section_id }}">{{ $section->section_name }}</option>
                    @endforeach
                </select>
            </div>

            <label class="modal-label">Remarks</label>
            <textarea class="modal-textarea" name="remarks" placeholder="Enter any remarks here.."></textarea>

            <div class="modal-actions">
                <button type="button" class="modal-cancel">Cancel</button>
                <button type="submit" class="modal-confirm">Confirm</button>
            </div>
        </form>
    </div>
</div>
@endsection
