<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // -------------------------
        // Card Counts
        // -------------------------
        $cardCounts = [
            'draft'           => Document::where('status', 'CREATED')->count(),
            'pending_receipt' => Document::where('status', 'PENDING')->count(),
            'pending_review'  => Document::where('status', 'UNDER REVIEW')->count(),
            'completed'       => Document::where('status', 'END OF CYCLE')->count(),
            'returned'        => Document::where('status', 'REOPENED')->count(),
            'cancelled'       => Document::where('is_active', 0)->count(),
            'total_documents' => Document::count(),
        ];

        // -------------------------
        // Base Query
        // -------------------------
        $query = Document::with(['type', 'currentSection', 'currentHolder'])
            ->where('status', '!=', 'CREATED') // Exclude draft documents
            ->where('created_at', '>=', now()->subMonth()) // Only last 1 month
            ->orderBy('created_at', 'desc');

        // -------------------------
        // Admin: Apply Search & Status Filters
        // -------------------------
        if (Auth::user()->role === 'ADMIN') {

            // 🔍 Search by document_number or document_name
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('document_number', 'like', "%{$search}%")
                    ->orWhere('document_name', 'like', "%{$search}%");
                });
            }

            // 🎯 Filter by status (optional)
            if ($request->filled('status')) {
                $status = $request->input('status');
                $query->where('status', $status);
            }

            $documents = $query->paginate(10)->withQueryString();

        } else {
            // Non-admin: only show last 10 documents from the last month
            $documents = $query->limit(10)->get();
        }

        return view('dashboard.index', compact('documents', 'cardCounts'));
    }
    // 🔥 REAL-TIME SEARCH ENDPOINT
    public function search(Request $request)
    {
        $documents = Document::with(['type', 'currentSection', 'currentHolder'])
            ->where('document_number', 'like', '%' . $request->search . '%')
            ->orWhere('document_name', 'like', '%' . $request->search . '%')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.search', compact('documents'))->render();
    }
}
