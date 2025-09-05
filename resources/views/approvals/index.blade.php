@extends('components.app')

@section('title', 'Pending Approvals')

@section('content')
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-md-6">
      <h2>Pending Approvals</h2>
    </div>
    <div class="col-md-6 text-right">
      <a href="{{ route('approvals.history') }}" class="btn btn-secondary">
        <i class="fas fa-history"></i> Approval History
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
              <th>Uploaded By</th>
              <th>Submitted At</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($approvals as $approval)
            <tr>
              <td>{{ $approval->document->document_id }}</td>
              <td>{{ $approval->document->title }}</td>
              <td>{{ $approval->document->department->name }}</td>
              <td>{{ $approval->document->uploader->name }}</td>
              <td>{{ $approval->created_at->format('M d, Y H:i') }}</td>
              <td>
                <div class="btn-group btn-group-sm" role="group">
                  <a href="{{ route('documents.show', $approval->document->id) }}" class="btn btn-info" title="View">
                    <i class="fas fa-eye"></i>
                  </a>
                  <form action="{{ route('approvals.approve', $approval->document->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success" title="Approve">
                      <i class="fas fa-check"></i>
                    </button>
                  </form>
                  <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectModal{{ $approval->document->id }}" title="Reject">
                    <i class="fas fa-times"></i>
                  </button>
                </div>

                <!-- Reject Modal -->
                <div class="modal fade" id="rejectModal{{ $approval->document->id }}" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="rejectModalLabel">Reject Document</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <form action="{{ route('approvals.reject', $approval->document->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                          <div class="form-group">
                            <label for="comments">Reason for Rejection *</label>
                            <textarea class="form-control" id="comments" name="comments" rows="3" required></textarea>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                          <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="text-center">No pending approvals found.</td>
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