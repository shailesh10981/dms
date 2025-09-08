@extends('components.app')
@section('title', 'Risk Report')
@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <div>
        <h3 class="card-title">{{ $report->title }} ({{ $report->risk_id }})</h3>
        <div class="text-muted">Type: {{ ucfirst($report->issue_type) }} | Status: {{ ucfirst($report->status) }}</div>
      </div>
      <div>
        <a href="{{ route('risk.reports.index') }}" class="btn btn-secondary">Back</a>
      </div>
    </div>
    <div class="card-body">
      <h5>Details</h5>
      <dl class="row">
        @foreach($fields as $f)
        <dt class="col-sm-3 text-capitalize">{{ str_replace('_',' ', $f) }}</dt>
        <dd class="col-sm-9">{{ $report->data[$f] ?? '—' }}</dd>
        @endforeach
        <dt class="col-sm-3">Attachment</dt>
        <dd class="col-sm-9">@if($report->attachment_path)<a href="{{ Storage::url($report->attachment_path) }}">Download</a>@else — @endif</dd>
      </dl>

      <h5>Workflow</h5>
      @php($flow = $report->workflow_definition ?? [])
      @if(!empty($flow))
        <ol>
          @foreach($flow as $uid)
            <li>{{ optional(\App\Models\User::find($uid))->name ?? 'User #'.$uid }} @if($report->current_approver_id==$uid) <span class="badge bg-info">Current</span>@endif</li>
          @endforeach
        </ol>
      @else
        <p class="text-muted">No approver chain defined.</p>
      @endif

      @if($report->status=='submitted' && $report->current_approver_id==auth()->id())
        <form method="POST" action="{{ route('risk.reports.approve', $report) }}" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-success">Approve</button>
        </form>
        <form method="POST" action="{{ route('risk.reports.reject', $report) }}" class="d-inline ms-2">
          @csrf
          <input type="text" name="rejection_reason" class="form-control d-inline-block" style="width: 220px" placeholder="Reason" required>
          <button type="submit" class="btn btn-danger">Reject</button>
        </form>
      @endif

      <h5 class="mt-4">History</h5>
      <p class="text-muted">Audit trail will appear here.</p>
    </div>
  </div>
</div>
@endsection
