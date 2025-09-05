@extends('components.app')

@section('title', 'View Compliance Report')

@section('content')
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-md-6">
      <h2>Compliance Report</h2>
      <h5 class="text-muted">{{ $report->title }}</h5>
    </div>
    <div class="col-md-6 text-right">
      <a href="{{ route('compliance.reports.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Reports
      </a>

      @can('update', $report)
      <a href="{{ route('compliance.reports.edit', $report) }}" class="btn btn-primary">
        <i class="fas fa-edit"></i> Edit
      </a>
      @endcan

      @if($report->status === 'draft' && $report->canBeSubmittedBy(auth()->user()))
      <form action="{{ route('compliance.reports.submit', $report) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-success">
          <i class="fas fa-paper-plane"></i> Submit
        </button>
      </form>
      @endif
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header">
      <h5 class="mb-0">Report Details</h5>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <p><strong>Report ID:</strong> {{ $report->report_id }}</p>
          <p><strong>Status:</strong>
            <span class="badge 
                            @if($report->status === 'draft') bg-secondary
                            @elseif($report->status === 'submitted') bg-primary
                            @elseif($report->status === 'approved') bg-success
                            @elseif($report->status === 'rejected') bg-danger @endif">
              {{ ucfirst($report->status) }}
            </span>
          </p>
          <p><strong>Template:</strong> {{ $report->template->name }}</p>
          <p><strong>Department:</strong> {{ $report->department->name }}</p>
        </div>
        <div class="col-md-6">
          <p><strong>Created By:</strong> {{ $report->creator->name }}</p>
          <p><strong>Created At:</strong> {{ $report->created_at->format('Y-m-d H:i') }}</p>
          @if($report->submitted_at)
          <p><strong>Submitted By:</strong> {{ $report->submitter->name }}</p>
          <p><strong>Submitted At:</strong> {{ $report->submitted_at->format('Y-m-d H:i') }}</p>
          @endif
          @if($report->approved_at)
          <p><strong>Approved By:</strong> {{ $report->approver->name }}</p>
          <p><strong>Approved At:</strong> {{ $report->approved_at->format('Y-m-d H:i') }}</p>
          @endif
          @if($report->rejection_reason)
          <p><strong>Rejection Reason:</strong> {{ $report->rejection_reason }}</p>
          @endif
        </div>
      </div>

      @if($report->description)
      <div class="mt-3">
        <h6>Description</h6>
        <p>{{ $report->description }}</p>
      </div>
      @endif
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header">
      <h5 class="mb-0">Report Data</h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead class="thead-light">
            <tr>
              <th width="30%">Field</th>
              <th>Value</th>
            </tr>
          </thead>
          <tbody>
            @foreach($fields as $field)
            <tr>
              <td>{{ $field->label }}</td>
              <td>
                @if($field->field_type === 'checkbox')
                {{ $report->getFieldValue($field->label) ? 'Yes' : 'No' }}
                @else
                {{ $report->getFieldValue($field->label) ?? 'N/A' }}
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  @if($report->approvals->isNotEmpty())
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Approval History</h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead class="thead-light">
            <tr>
              <th>Approver</th>
              <th>Status</th>
              <th>Comments</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            @foreach($approvals as $approval)
            <tr>
              <td>{{ $approval->user->name }}</td>
              <td>
                <span class="badge 
                                            @if($approval->status === 'approved') bg-success
                                            @elseif($approval->status === 'rejected') bg-danger
                                            @else bg-secondary @endif">
                  {{ ucfirst($approval->status) }}
                </span>
              </td>
              <td>{{ $approval->comments ?? 'N/A' }}</td>
              <td>{{ $approval->acted_at ? $approval->acted_at->format('Y-m-d H:i') : 'Pending' }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif
  {{-- ✅ Include Audit Logs Below --}}
  @include('compliance.reports.partials.audit-log')
  @if($report->status === 'submitted' && auth()->user()->can('approve', $report))
  <div class="row mt-4">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-success text-white">
          <h5 class="mb-0">Approve Report</h5>
        </div>
        <div class="card-body">
          <form action="{{ route('compliance.reports.approve', $report) }}" method="POST">
            @csrf
            <div class="form-group">
              <label for="approval_comments">Comments (Optional)</label>
              <textarea class="form-control" id="approval_comments" name="comments" rows="2"></textarea>
            </div>
            <button type="submit" class="btn btn-success">
              <i class="fas fa-check"></i> Approve Report
            </button>
          </form>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-danger text-white">
          <h5 class="mb-0">Reject Report</h5>
        </div>
        <div class="card-body">
          <form action="{{ route('compliance.reports.reject', $report) }}" method="POST">
            @csrf
            <div class="form-group">
              <label for="rejection_reason">Reason for Rejection *</label>
              <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="2" required></textarea>
            </div>
            <button type="submit" class="btn btn-danger">
              <i class="fas fa-times"></i> Reject Report
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
  @endif
</div>
@endsection