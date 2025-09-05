<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentAuditLogController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', DocumentAuditLog::class);

        $query = DocumentAuditLog::with(['document', 'user'])
            ->latest();

        // Apply filters
        if ($request->filled('document_id')) {
            $query->where('document_id', $request->document_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // For non-admin users, restrict to their department's documents
        if (!Auth::user()->hasRole('admin')) {
            $query->whereHas('document', function ($q) {
                $q->where('department_id', Auth::user()->department_id);
            });
        }

        $logs = $query->paginate(50);

        return view('audit-logs.index', compact('logs'));
    }
}
