<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\DocumentAttachment;
use App\Models\Section;
use App\Models\Session;
use App\Models\DocumentAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\PDF;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = strtoupper($user->role ?? '');
        $sections = Section::orderBy('section_name')->get();

        // COMMON FILTERS
        $searchFilter = function ($query, $search) {
            $query->where(fn($q) =>
                $q->where('document_number', 'like', "%{$search}%")
                ->orWhere('document_name', 'like', "%{$search}%")
                ->orWhereHas('createdBy', fn($q2) =>
                    $q2->whereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$search}%"])
                )
            );
        };

        $mySearch   = $request->input('my_search');
        $myStatus   = $request->input('my_status');
        $allSearch  = $request->input('all_search');
        $allStatus  = $request->input('all_status');
        $mySectionId = $request->input('my_section_id');
        $allSectionId = $request->input('section_id');

        // ------------------------------
        // COLUMN 1: MY HANDLED DOCUMENTS
        // ------------------------------
        $myDocuments = Document::with(['type', 'currentSection'])
            ->where('current_section_id', $user->section_id)
            ->where(function ($query) use ($user) {
                $query->whereIn('status', ['UNDER REVIEW', 'REOPENED', 'FORWARDED'])
                    ->orWhere(fn($q) => $q->where('status', 'END OF CYCLE')
                                            ->where('current_section_id', $user->section_id))
                    ->orWhere(fn($q) => $q->where('status', 'CREATED')
                                            ->where('originating_section_id', $user->section_id));
            })
            ->when($mySearch, fn($q) => $searchFilter($q, $mySearch))
            ->when($myStatus, fn($q) => $q->where('status', $myStatus))
            ->orderBy('updated_at', 'desc')
            ->paginate(10, ['*'], 'my_docs')
            ->withQueryString();

        // ------------------------------
        // COLUMN 1: CREATED DOCUMENTS
        // ------------------------------
        $myCreatedDocuments = Document::with(['type', 'currentSection'])
            ->when(true, function ($query) use ($role, $user, $mySectionId) {
                if (in_array($role, ['SECTION-HEAD', 'EMPLOYEE'])) {
                    $query->where('originating_section_id', $user->section_id);
                } elseif ($role === 'DEPARTMENT-HEAD') {
                    $sectionIds = Section::where('department_id', $user->section->department->department_id)
                        ->pluck('section_id');
                    $query->when($mySectionId, fn($q) => $q->where('originating_section_id', $mySectionId))
                        ->when(!$mySectionId, fn($q) => $q->whereIn('originating_section_id', $sectionIds));
                } elseif ($role === 'ADMIN') {
                    $query->when($mySectionId, fn($q) => $q->where('originating_section_id', $mySectionId));
                }
            })
            ->when($mySearch, fn($q) => $searchFilter($q, $mySearch))
            ->when($myStatus, fn($q) => $q->where('status', $myStatus))
            ->orderBy('updated_at', 'desc')
            ->paginate(10, ['*'], 'created_docs')
            ->withQueryString();

        // ------------------------------
        // COLUMN 2: PENDING DOCUMENTS
        // ------------------------------
        $pendingDocuments = Document::with(['type','currentSection'])
            ->where('current_section_id', $user->section_id)
            ->where('status', 'PENDING')
            ->when($allSearch, fn($q) => $searchFilter($q, $allSearch))
            ->orderBy('updated_at', 'desc')
            ->paginate(10, ['*'], 'pending_docs')
            ->withQueryString();

        // ------------------------------
        // COLUMN 2: FORWARDED DOCUMENTS
        // ------------------------------
        $forwardedDocuments = Document::with([
                'type', 'currentSection', 'originatingSection',
                'actions' => fn($q) => $q->latest('action_datetime'),
                'actions.user', 'actions.section'
            ])
            ->where('status', '!=', 'CREATED')
            ->whereHas('actions', function ($q) use ($allSectionId) {
                $q->whereIn('action_type', ['RECEIVED', 'FORWARDED'])
                ->when($allSectionId && $allSectionId !== 'all', fn($q2) => $q2->where('section_id', $allSectionId));
            })
            ->when($allSearch, fn($q) => $searchFilter($q, $allSearch))
            ->when($allStatus, fn($q) => $q->where('status', $allStatus))
            ->orderBy('updated_at', 'desc')
            ->paginate(10, ['*'], 'forwarded_docs')
            ->withQueryString();

        // ------------------------------
        // COLUMN 2: ALL DOCUMENTS (Except CREATED)
        // ------------------------------
        $allDocuments = Document::with(['type', 'currentSection'])
            ->whereNotIn('status', ['CREATED'])
            ->when($allSearch, fn($q) => $searchFilter($q, $allSearch))
            ->when($allStatus, fn($q) => $q->where('status', $allStatus))
            ->orderBy('updated_at', 'desc')
            ->paginate(10, ['*'], 'all_docs')
            ->withQueryString();

        return view('documents.index', compact(
            'myDocuments',
            'myCreatedDocuments',
            'pendingDocuments',
            'forwardedDocuments',
            'allDocuments',
            'sections',
            'role',
            'user'
        ));
    }

    public function create()
    {
        $user = Auth::user();

        // Active document types only
        $types = DocumentType::where('is_active', 1)->get();

        // Department + Section codes from logged-in user
        $department_code = strtoupper(
            $user->section->department->department_code ?? 'DEPT'
        );

        $section_code = strtoupper(
            $user->section->section_code ?? 'SECTION'
        );

        // Global increment (can be scoped later)
        $lastDoc = Document::orderBy('doc_id', 'desc')->first();
        $nextDocId = $lastDoc ? $lastDoc->doc_id + 1 : 1;
        $nextDocIdPadded = str_pad($nextDocId, 3, '0', STR_PAD_LEFT);

        return view('documents.add', [
            'types'            => $types,
            'department_code'  => $department_code,
            'section_code'     => $section_code,
            'nextDocIdPadded'  => $nextDocIdPadded,
            'year'             => now()->year,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'type_id'         => 'required|exists:document_types,type_id',
                'document_name'   => 'required|string|max:255',
                'attachments.*'   => 'nullable|file|mimes:jpg,jpeg,png,pdf,txt|max:10240',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        }

        $user = Auth::user();

        // Codes from user context
        $department_code = strtoupper($user->section->department->department_code ?? 'DEPT');
        $section_code    = strtoupper($user->section->section_code ?? 'SECTION');

        // Document type code
        $type = DocumentType::findOrFail($request->type_id);
        $type_code = strtoupper($type->type_code);

        // Next document ID
        $lastDoc = Document::orderBy('doc_id', 'desc')->first();
        $nextDocId = $lastDoc ? $lastDoc->doc_id + 1 : 1;
        $nextDocIdPadded = str_pad($nextDocId, 3, '0', STR_PAD_LEFT);

        // Final document number
        $document_number = "{$department_code}-{$section_code}-{$type_code}-" . now()->year . "-{$nextDocIdPadded}";

        // Create document
        $document = Document::create([
            'document_number'        => $document_number,
            'document_name'          => $request->document_name,
            'type_id'                => $request->type_id,
            'originating_section_id' => $user->section_id,
            'current_section_id'     => $user->section_id,
            'current_holder_id'      => $user->user_id,
            'created_by'             => $user->user_id,
            'status'                 => 'CREATED',
            'is_active'              => 1,
        ]);

        // Record the creation action
        DocumentAction::create([
            'doc_id'          => $document->doc_id,
            'section_id'      => $user->section_id,
            'user_id'         => $user->user_id,
            'action_type'     => 'CREATED',
            'remarks'         => 'Document created',
            'action_datetime' => now(),
        ]);

        // --- Generate PDF ---
           $pdf = app(\Barryvdh\DomPDF\PDF::class)->loadView('documents.pdf', [
                'document_number' => $document_number,
                'document_name'   => $request->document_name,
                'created_at'      => now()->format('F j, Y'),
                'owner_name'      => $user->full_name,
            ]);

            $pdfFileName = 'DOC_' . $document_number . '.pdf';
            $pdfPath = 'documents/' . $pdfFileName;

            // Save PDF to storage
            Storage::disk('public')->put($pdfPath, $pdf->output());

            // Attach PDF automatically
            DocumentAttachment::create([
                'doc_id'             => $document->doc_id,
                'file_original_name' => $pdfFileName,
                'file_stored_name'   => $pdfFileName,
                'file_path'          => $pdfPath,
                'file_type'          => 'application/pdf',
                'file_size'          => Storage::disk('public')->size($pdfPath),
                'version_number'     => 1,
                'is_active'          => 1,
                'uploaded_by'        => $user->user_id,
                'uploaded_at'        => now(),
            ]);

            // --- Handle other attachments ---
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $storedName = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('documents', $storedName, 'public');

                    DocumentAttachment::create([
                        'doc_id'             => $document->doc_id,
                        'file_original_name' => $file->getClientOriginalName(),
                        'file_stored_name'   => $storedName,
                        'file_path'          => $filePath,
                        'file_type'          => $file->getClientMimeType(),
                        'file_size'          => $file->getSize(),
                        'version_number'     => 1,
                        'is_active'          => 1,
                        'uploaded_by'        => $user->user_id,
                        'uploaded_at'        => now(),
                    ]);
                }
            }

            return redirect()
                ->route('documents.index')
                ->with('success', 'Document created successfully! PDF attached automatically.');
    }

    public function show(Document $doc)
    {
        $document = $doc->load([
            'type',
            'originatingSection',
            'currentSection',
            'attachments',
            'actions.section',
            'actions.user',
            'currentHolder',
            'createdBy',
        ]);

        $sections = Section::where('is_active', 1)->orderBy('section_name')->get();
        return view('documents.view', compact('document', 'sections'));
    }

    public function performAction(Request $request, Document $doc)
    {
        $request->validate([
            'action_type' => 'required|string|in:Accept,Forward,End Cycle,Reopen',
            'remarks'     => 'nullable|string|max:500',
            'section_id'  => 'nullable|exists:sections,section_id', // only for Forward
        ]);

        $user = Auth::user();

        // If forwarding, section_id is required
        if ($request->action_type === 'Forward' && !$request->section_id) {
            return back()->withErrors(['section_id' => 'You must select a section to forward the document.']);
        }

        // Map UI actions to DB ENUM values
        $actionMap = [
            'Accept'     => 'RECEIVED',
            'Forward'    => 'FORWARDED',
            'End Cycle'  => 'END OF CYCLE',
            'Reopen'     => 'REOPEN',
        ];

        $statusMap = [
            'Accept'     => 'UNDER REVIEW', // document status
            'Forward'    => 'PENDING',
            'End Cycle'  => 'END OF CYCLE',
            'Reopen'     => 'REOPENED',
        ];

        $dbActionType = $actionMap[$request->action_type];
        $newStatus    = $statusMap[$request->action_type];

        // Default remarks based on action
        $actionLabels = [
            'Accept' => 'Received',
            'Forward' => 'Forwarded',
            'End Cycle' => 'Ended',
            'Reopen' => 'Reopened',
        ];
        // Use custom remarks if provided, otherwise use default
        $remarks = $request->filled('remarks')
            ? $request->remarks
            : $actionLabels[$request->action_type] . " by {$user->full_name}";

        DocumentAction::create([
            'doc_id'          => $doc->doc_id,
            'section_id'      => $request->section_id ?? $doc->current_section_id,
            'position_id'     => $user->position_id,
            'user_id'         => $user->user_id,
            'action_type'     => $dbActionType,
            'remarks'         => $remarks,
            'action_datetime' => now(),
        ]);

        // Update document status and current holder
        $updateData = [
            'status' => $newStatus,
        ];

        if ($request->action_type === 'Forward') {
            $updateData['current_section_id'] = $request->section_id;

            // Assign next holder
            $nextHolder = \App\Models\User::where('section_id', $request->section_id)
                ->where('is_active', 1)
                ->orderBy('role')
                ->first();

            $updateData['current_holder_id'] = $nextHolder ? $nextHolder->user_id : $doc->current_holder_id;
        } else {
            $updateData['current_holder_id'] = $user->user_id;
        }

        $doc->update($updateData);

        return redirect()->route('documents.show', $doc->doc_id)
                        ->with('success', 'Action performed successfully!');
    }

    public function destroy($doc)
    {
        $document = Document::findOrFail($doc);

        if ($document->status !== 'CREATED') {
            return back()->with('error', 'Only documents with status CREATED can be deleted.');
        }

        $document->load(['attachments', 'actions']);

        foreach ($document->attachments as $attachment) {
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }
        }

        $document->attachments()->delete();
        $document->actions()->delete();
        $document->delete();

        return redirect()
            ->route('documents.index')
            ->with('success', 'Document deleted successfully.');
    }
}
