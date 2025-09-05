@extends('components.app')

@section('title', 'Approval History')

@section('content')
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-md-6">
      <h2>Approval History</h2>
    </div>
    <div class="col-md-6 text-right">
      <a href="{{ route('approvals.index') }}" class="btn btn-secondary">
        <i class="fas fa-tasks"></i> Pending Approvals
      </a>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead class="thead-light">
            <tr>
              <th>Document ID</th>
              <th>Title</th>
              <th>Department</th>
              <th>Status</th>
              <th>Approver</th>
              <th>Action Date</th>
              <th>Comments</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($approvals as $approval)
            <tr>
              <td>{{ $approval->document->document_id }}</td>
              <td>{{ $approval->document->title }}</td>
              <td>{{ $approval->document->department->name }}</td>
              <td>
                @if($approval->status == 'approved')
                <span class="badge bg-success">Approved</span>
                @else
                <span class="badge bg-danger">Rejected</span>
                @endif
              </td>
              <td>{{ $approval->approver->name }}</td>
              <td>{{ $approval->approved_at->format('M d, Y H:i') }}</td>
              <td>{{ $approval->comments ?: 'N/A' }}</td>
              <td>
                <a href="{{ route('documents.show', $approval->document->id) }}" class="btn btn-sm btn-info" title="View">
                  <i class="fas fa-eye"></i>
                </a>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="8" class="text-center">No approval history found.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-center">
        {{ $approvals->links() }}
      </div>
    </div>
  </div>
</div>
@endsection