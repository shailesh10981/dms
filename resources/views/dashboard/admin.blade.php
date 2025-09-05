@extends('components.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row">
  <div class="col-lg-3 col-6">
    <div class="small-box bg-info">
      <div class="inner">
        <h3>{{ App\Models\ComplianceReport::count() }}</h3>
        <p>Total Reports</p>
      </div>
      <div class="icon">
        <i class="fas fa-file-alt"></i>
      </div>
      <a href="{{ route('compliance.reports.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>

  <div class="col-lg-3 col-6">
    <div class="small-box bg-warning">
      <div class="inner">
        <h3>{{ App\Models\ComplianceReport::where('status', 'submitted')->count() }}</h3>
        <p>Pending Approval</p>
      </div>
      <div class="icon">
        <i class="fas fa-hourglass-half"></i>
      </div>
      <a href="{{ route('compliance.reports.index') }}?status=submitted" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>

  <div class="col-lg-3 col-6">
    <div class="small-box bg-warning">
      <div class="inner">
        <h3>0</h3>
        <p>Upcoming Reports</p>
      </div>
      <div class="icon">
        <i class="fas fa-clipboard-list"></i>
      </div>
      <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>

  <div class="col-lg-3 col-6">
    <div class="small-box bg-danger">
      <div class="inner">
        <h3>0</h3>
        <p>Overdue Reports</p>
      </div>
      <div class="icon">
        <i class="fas fa-exclamation-circle"></i>
      </div>
      <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Recent Documents</h3>
      </div>
      <div class="card-body">
        <p>No recent documents found.</p>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">System Statistics</h3>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-6">
            <p>Total Users: {{ \App\Models\User::count() }}</p>
            <p>Active Departments: {{ \App\Models\Department::count() }}</p>
          </div>
          <div class="col-6">
            <p>Storage Used: 0 MB</p>
            <p>System Version: 1.0</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection