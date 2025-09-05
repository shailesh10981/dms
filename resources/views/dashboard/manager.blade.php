@extends('components.app')

@section('title', 'Manager Dashboard')

@section('content')
<div class="row">
  <div class="col-lg-4 col-6">
    <div class="small-box bg-info">
      <div class="inner">
        <h3>0</h3>
        <p>Pending Approvals</p>
      </div>
      <div class="icon">
        <i class="fas fa-check-circle"></i>
      </div>
      <a href="#" class="small-box-footer">Review Now <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>

  <div class="col-lg-4 col-6">
    <div class="small-box bg-success">
      <div class="inner">
        <h3>0</h3>
        <p>Approved Documents</p>
      </div>
      <div class="icon">
        <i class="fas fa-file-alt"></i>
      </div>
      <a href="#" class="small-box-footer">View All <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>

  <div class="col-lg-4 col-6">
    <div class="small-box bg-warning">
      <div class="inner">
        <h3>0</h3>
        <p>Team Members</p>
      </div>
      <div class="icon">
        <i class="fas fa-users"></i>
      </div>
      <a href="#" class="small-box-footer">Manage Team <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Approval Queue</h3>
      </div>
      <div class="card-body">
        <p>No documents pending approval.</p>
      </div>
    </div>
  </div>
</div>
@endsection