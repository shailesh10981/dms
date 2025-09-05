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
      <p>Defined at submission time.</p>

      <h5>History</h5>
      <p>Audit trail will appear here.</p>
    </div>
  </div>
</div>
@endsection
