<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Department;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $role = strtoupper($user->role ?? '');

        $isAdmin = $role === 'ADMIN';
        $isChief = $role === 'CHIEF';
        $isDivHead = $role === 'DEPARTMENT-HEAD';
        $isSecHead = $role === 'SECTION-HEAD';

        $isFilterAllowed = $isAdmin || $isChief;

        // -----------------------------
        // Base filtered query (SINGLE SOURCE)
        // -----------------------------
        $baseQuery = $this->applyFilters(Document::query(), $request);

        // -----------------------------
        // Status labels (INCLUDE CREATED)
        // -----------------------------
        $statuses = ['PENDING','UNDER REVIEW','END OF CYCLE','REOPENED'];

        $statusLabels = [
            'PENDING'      => 'Pending',
            'UNDER REVIEW' => 'Under Review',
            'END OF CYCLE' => 'End of Cycle',
            'REOPENED'     => 'Reopened',
        ];

        $labels = array_values($statusLabels);

        // -----------------------------
        // KPI Card Counts (SAFE + FILTERED)
        // -----------------------------
        $docStats = (clone $baseQuery)
            ->select('documents.status', DB::raw('COUNT(*) as count'))
            ->groupBy('documents.status')
            ->get()
            ->keyBy('status');

        $counts = collect($statuses)->mapWithKeys(function ($s) use ($docStats) {
            return [$s => $docStats[$s]->count ?? 0];
        });

        // Cancelled (FILTERED also)
        $cancelledCount = (clone $baseQuery)
            ->where('documents.is_active', 0)
            ->count();

        $cardCounts = [
            'pending_receipt' => $counts['PENDING'],
            'pending_review'  => $counts['UNDER REVIEW'],
            'end'       => $counts['END OF CYCLE'],
            'reopened'        => $counts['REOPENED'],
            'cancelled'       => $cancelledCount,
        ];

        $cardCounts['total_documents'] = $counts->sum() + $cancelledCount;

        // -----------------------------
        // Section summary + chart
        // -----------------------------
        $sectionStats = (clone $baseQuery)
            ->join('sections', 'documents.current_section_id', '=', 'sections.section_id')
            ->whereNotNull('documents.current_section_id')
            ->select(
                'sections.section_name',
                'documents.status',
                DB::raw('COUNT(*) as total')
            )
            ->where('status', '!=', 'CREATED')
            ->groupBy('sections.section_name', 'documents.status')
            ->get();

        $sectionNames = $sectionStats->pluck('section_name')->unique()->values()->toArray();

        if (empty($sectionNames)) {
            $sectionNames = Section::pluck('section_name')->toArray();
        }

        // Chart dataset
        $sectionChartData = [];
        foreach ($statuses as $status) {

            $data = [];

            foreach ($sectionNames as $sectionName) {
                $row = $sectionStats->first(function ($item) use ($sectionName, $status) {
                    return $item->section_name === $sectionName && $item->status === $status;
                });

                $data[] = $row->total ?? 0;
            }

            $sectionChartData[] = [
                'label' => $statusLabels[$status],
                'data'  => $data
            ];
        }

        // Table summary
        $sectionSummary = [];
        foreach ($sectionStats as $row) {
            $sectionSummary[$row->section_name][$statusLabels[$row->status]] = $row->total;
        }

        foreach ($sectionSummary as &$data) {
            foreach ($statusLabels as $label) {
                $data[$label] = $data[$label] ?? 0;
            }
        }

        // -----------------------------
        // Trend chart
        // -----------------------------
        $period = $request->get('period', 'month');

        switch ($period) {
            case 'day':   $format = '%Y-%m-%d'; break;
            case 'week':  $format = '%x-W%v';   break;
            case 'year':  $format = '%Y';       break;
            default:      $format = '%Y-%m';    break;
        }

        $trendData = (clone $baseQuery)
            ->select(
                DB::raw("DATE_FORMAT(documents.created_at, '$format') as period"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        $trendLabels = $trendData->pluck('period')->map(function ($date) use ($period) {

            if ($period === 'day') {
                return \Carbon\Carbon::parse($date)->format('F j, Y');
            }

            if ($period === 'month') {
                return \Carbon\Carbon::createFromFormat('Y-m', $date)->format('F Y');
            }

            if ($period === 'year') {
                return $date;
            }

            if ($period === 'week') {
                [$year, $week] = explode('-W', $date);
                return "Week $week, $year";
            }

            return $date;
        });

        $trendCounts = $trendData->pluck('total');

        // -----------------------------
        // Filters (for dropdowns)
        // -----------------------------
        $documentTypes = DocumentType::all();
        $departments   = Department::all();
        $sections      = Section::all();

        // -----------------------------
        // Return
        // -----------------------------
        return view('reports.index', compact(
            'labels',
            'counts',
            'cardCounts',
            'sectionNames',
            'sectionChartData',
            'sectionSummary',
            'documentTypes',
            'departments',
            'sections',
            'statuses',
            'trendLabels',
            'trendCounts',
            'period',
            'isFilterAllowed',
            'isAdmin',
            'isChief',
            'isDivHead',
            'isSecHead'
        ));
    }

    // -----------------------------
    // PDF export
    // -----------------------------
    public function exportPDF(Request $request)
    {
        $documents = $this->applyFilters(Document::query(), $request)->get();
        $pdf = Pdf::loadView('reports.pdf', compact('documents'));
        return $pdf->download('report.pdf');
    }

    // -----------------------------
    // CSV export
    // -----------------------------
    public function exportCSV(Request $request)
    {
        $documents = $this->applyFilters(Document::query(), $request)->get();
        $filename = "report.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function() use ($documents) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Document ID','Number', 'Title','Type','Status','Department','Section','Created At']);
            foreach ($documents as $doc) {
                fputcsv($handle, [
                    $doc->doc_id,
                    $doc->document_number,
                    $doc->document_name,
                    $doc->type->type_name ?? '',
                    $doc->status,
                    $doc->currentSection->department->department_name  ?? '',
                    $doc->currentSection->section_name ?? '',
                    $doc->created_at
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // -----------------------------
    // Apply filters (reusable)
    // -----------------------------
    private function applyFilters($query, $request)
    {
        $user = $request->user();

        $role = strtoupper($user->role ?? '');

        $isAdmin = $role === 'ADMIN';
        $isChief = $role === 'CHIEF';
        $isDivisionHead = $role === 'DEPARTMENT-HEAD';

        // =============================
        // ADMIN / CHIEF → FULL ACCESS
        // =============================
        if ($isAdmin || $isChief) {

            if ($request->filled('filter_by') && $request->filled('dept_or_sec')) {

                $value = $request->dept_or_sec;

                // ✅ If ALL → do not apply any department/section filter
                if ($value !== 'all') {

                    if ($request->filter_by === 'department') {

                        $sectionIds = Section::where('department_id', $value)
                            ->pluck('section_id');

                        $query->whereIn('documents.current_section_id', $sectionIds);
                    }

                    if ($request->filter_by === 'section') {

                        $query->where('documents.current_section_id', $value);
                    }
                }
            }
        }

        // =============================
        // DIVISION HEAD → ONLY OWN DEPT
        // =============================
        elseif ($isDivisionHead) {

            $departmentId = $user->section->department_id ?? null;

            if ($departmentId) {

                $sectionIds = Section::where('department_id', $departmentId)
                    ->pluck('section_id');

                $query->whereIn('documents.current_section_id', $sectionIds);

                if ($request->filter_by === 'section' && $request->filled('dept_or_sec')) {
                    $query->where('documents.current_section_id', $request->dept_or_sec);
                }
            }
        }

        // =============================
        // NORMAL USER
        // =============================
        else {

            if ($user->section_id) {
                $query->where('documents.current_section_id', $user->section_id);
            }
        }

        return $this->applyCommonFilters($query, $request);
    }

    private function applyCommonFilters($query, $request)
    {
        if ($request->from) {
            $query->whereDate('documents.created_at', '>=', $request->from);
        }

        if ($request->to) {
            $query->whereDate('documents.created_at', '<=', $request->to);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->type) {
            $query->where('type_id', $request->type);
        }

        return $query;
    }

}
