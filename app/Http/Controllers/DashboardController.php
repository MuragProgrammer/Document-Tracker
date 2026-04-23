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
            'pending_receipt' => Document::where('status', 'PENDING')->count(),
            'pending_review'  => Document::where('status', 'UNDER REVIEW')->count(),
            'end'       => Document::where('status', 'END OF CYCLE')->count(),
            'reopened'        => Document::where('status', 'REOPENED')->count(),
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

    public function search(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $documents = Document::with(['type', 'currentSection', 'currentHolder'])
            ->where('status', '!=', 'CREATED')
            ->where('created_at', '>=', now()->subMonth())
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {

                    // 🔍 Document fields
                    $query->where('document_number', 'like', "%{$search}%")
                        ->orWhere('document_name', 'like', "%{$search}%")

                    // 🔍 Search in currentHolder (User)
                        ->orWhereHas('currentHolder', function ($q2) use ($search) {
                            $q2->whereRaw(
                                "CONCAT(first_name, ' ', last_name) LIKE ?
                                OR first_name LIKE ?
                                OR last_name LIKE ?",
                                ["%{$search}%", "%{$search}%", "%{$search}%"]
                            );
                        });

                });
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.search', compact('documents', 'search'))->render();
    }
}
