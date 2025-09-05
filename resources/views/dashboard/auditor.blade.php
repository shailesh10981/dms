@extends('components.app')

@section('title', 'Auditor Dashboard')

@section('content')
<div class="row">
  <div class="col-lg-4 col-6">
    <div class="small-box bg-info">
      <div class="inner">
        <h3>0</h3>
        <p>Documents Reviewed</p>
      </div>
      <div class="icon">
        <i class="fas fa-file"></i>
      </div>
      <a href="#" class="small-box-footer">View All <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>

  <div class="col-lg-4 col-6">
    <div class="small-box bg-success">
      <div class="inner">
        <h3>0</h3>
        <p>Reports Audited</p>
      </div>
      <div class="icon">
        <i class="fas fa-clipboard-check"></i>
      </div>
      <a href="#" class="small-box-footer">View All <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>

  <div class="col-lg-4 col-6">
    <div class="small-box bg-warning">
      <div class="inner">
        <h3>0</h3>
        <p>Audit Findings</p>
      </div>
      <div class="icon">
        <i class="fas fa-search"></i>
      </div>
      <a href="#" class="small-box-footer">View Details <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Recent Audit Activities</h3>
      </div>
      <div class="card-body">
        <p>No recent audit activities found.</p>
      </div>
    </div>
  </div>
</div>
@endsection