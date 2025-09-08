@extends('components.app')
@section('title', 'Risk Reports')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between mb-3">
    <a href="{{ route('risk.reports.create') }}" class="btn btn-primary">New Risk Report</a>
  </div>
  <div class="card">
    <div class="card-body table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Risk ID</th><th>Title</th><th>Issue Type</th><th>Status</th><th>Department</th><th>Current Approver</th><th>Created</th><th></th>
          </tr>
        </thead>
        <tbody>
          @foreach($reports as $r)
          <tr>
            <td>{{ $r->risk_id }}</td>
            <td>{{ $r->title }}</td>
            <td>{{ ucfirst($r->issue_type) }}</td>
            <td>{{ ucfirst($r->status) }}</td>
            <td>{{ $r->department->name ?? '—' }}</td>
            <td>{{ optional(\App\Models\User::find($r->current_approver_id))->name ?? '—' }}</td>
            <td>{{ $r->created_at }}
            <td><a href="{{ route('risk.reports.show', $r) }}" class="btn btn-sm btn-outline-secondary">View</a></td>
          </tr>
          @endforeach
        </tbody>
      </table>
      {{ $reports->links() }}
    </div>
  </div>
</div>
@endsection
