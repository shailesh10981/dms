<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Document;
use App\Models\DocumentApproval;
use App\Models\RiskReport;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return $this->adminDashboard();
        } elseif ($user->hasRole('manager')) {
            return $this->managerDashboard();
        } elseif ($user->hasRole('compliance_officer')) {
            return $this->complianceDashboard();
        } elseif ($user->hasRole('auditor')) {
            return $this->auditorDashboard();
        } else {
            return $this->userDashboard();
        }
    }

    protected function commonData(): array
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole('admin');

        $docQuery = Document::query();
        if (!$isAdmin) {
            $docQuery->where('department_id', $user->department_id);
        }

        $totalDocuments = (clone $docQuery)->count();
        $publicDocuments = (clone $docQuery)->where('visibility', 'Public')->count();
        $publishDocuments = (clone $docQuery)->where('visibility', 'Publish')->count();
        $privateDocuments = (clone $docQuery)->where('visibility', 'Private')->count();

        $pendingDocApprovals = DocumentApproval::with(['document.department','document.uploader'])
            ->where('approver_id', $user->id)
            ->where('status', 'pending')
            ->latest()->limit(5)->get();

        $pendingRisk = RiskReport::with(['department','creator'])
            ->where('current_approver_id', $user->id)
            ->where('status','submitted')
            ->latest()->limit(5)->get();

        $recentDocuments = (clone $docQuery)->with('department')->latest()->limit(5)->get();
        $recentRisks = RiskReport::with('department')->when(!$isAdmin, fn($q)=>$q->where('department_id', $user->department_id))->latest()->limit(5)->get();

        return compact(
            'totalDocuments','publicDocuments','publishDocuments','privateDocuments',
            'pendingDocApprovals','pendingRisk','recentDocuments','recentRisks'
        );
    }

    protected function adminDashboard()
    {
        return view('dashboard.admin', array_merge(['title' => 'Admin Dashboard'], $this->commonData()));
    }

    protected function managerDashboard()
    {
        return view('dashboard.manager', array_merge(['title' => 'Manager Dashboard'], $this->commonData()));
    }

    protected function complianceDashboard()
    {
        return view('dashboard.compliance', array_merge(['title' => 'Compliance Officer Dashboard'], $this->commonData()));
    }

    protected function auditorDashboard()
    {
        return view('dashboard.auditor', array_merge(['title' => 'Auditor Dashboard'], $this->commonData()));
    }

    protected function userDashboard()
    {
        return view('dashboard.user', array_merge(['title' => 'User Dashboard'], $this->commonData()));
    }
}
