@extends('components.app')

@section('title', 'Compliance Dashboard')

@section('content')
<div class="row">
  <div class="col-lg-3 col-6">
    <div class="small-box bg-info">
      <div class="inner">
        <h3>0</h3>
        <p>Reports Due</p>
      </div>
      <div class="icon">
        <i class="fas fa-calendar"></i>
      </div>
      <a href="#" class="small-box-footer">View All <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>

  <div class="col-lg-3 col-6">
    <div class="small-box bg-success">
      <div class="inner">
        <h3>0</h3>
        <p>Submitted Reports</p>
      </div>
      <div class="icon">
        <i class="fas fa-check-circle"></i>
      </div>
      <a href="#" class="small-box-footer">View All <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>

  <div class="col-lg-3 col-6">
    <div class="small-box bg-info">
      <div class="inner">
        <h3>{{ auth()->user()->department->complianceReports()->where('status', 'draft')->count() }}</h3>
        <p>Draft Reports</p>
      </div>
      <div class="icon">
        <i class="fas fa-edit"></i>
      </div>
      <a href="{{ route('compliance.reports.index') }}?status=draft" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>

  <div class="col-lg-3 col-6">
    <div class="small-box bg-danger">
      <div class="inner">
        <h3>0</h3>
        <p>Overdue Reports</p>
      </div>
      <div class="icon">
        <i class="fas fa-exclamation-triangle"></i>
      </div>
      <a href="#" class="small-box-footer">Take Action <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Upcoming Report Deadlines</h3>
      </div>
      <div class="card-body">
        <p>No upcoming deadlines in the next 7 days.</p>
      </div>
    </div>
  </div>
</div>
@endsection