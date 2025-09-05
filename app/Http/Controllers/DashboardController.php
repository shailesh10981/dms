<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    protected function adminDashboard()
    {
        return view('dashboard.admin', [
            'title' => 'Admin Dashboard',
        ]);
    }

    protected function managerDashboard()
    {
        return view('dashboard.manager', [
            'title' => 'Manager Dashboard',
        ]);
    }

    protected function complianceDashboard()
    {
        return view('dashboard.compliance', [
            'title' => 'Compliance Officer Dashboard',
        ]);
    }

    protected function auditorDashboard()
    {
        return view('dashboard.auditor', [
            'title' => 'Auditor Dashboard',
        ]);
    }

    protected function userDashboard()
    {
        return view('dashboard.user', [
            'title' => 'User Dashboard',
        ]);
    }
}
