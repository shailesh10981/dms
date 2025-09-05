@extends('components.app')

@section('title', 'Trash')

@section('content')
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-md-6">
      <h2>Deleted Documents</h2>
    </div>
    <div class="col-md-6 text-right">
      <a href="{{ route('documents.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Documents
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
              <th>Deleted By</th>
              <th>Deleted At</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($documents as $document)
            <tr>
              <td>{{ $document->document_id }}</td>
              <td>{{ $document->title }}</td>
              <td>{{ $document->department->name }}</td>
              <td>{{ $document->uploader->name }}</td>
              <td>{{ $document->deleted_at->format('M d, Y H:i') }}</td>
              <td>
                <div class="btn-group btn-group-sm" role="group">
                  <form action="{{ route('documents.restore', $document->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success" title="Restore">
                      <i class="fas fa-trash-restore"></i>
                    </button>
                  </form>
                  @can('document_delete')
                  <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal{{ $document->id }}" title="Permanently Delete">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  @endcan
                </div>

                <!-- Delete Modal -->
                <div class="modal fade" id="deleteModal{{ $document->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirm Permanent Deletion</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <p>Are you sure you want to permanently delete this document? This action cannot be undone.</p>
                        <p><strong>Document:</strong> {{ $document->title }} ({{ $document->document_id }})</p>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <form action="{{ route('documents.force-delete', $document->id) }}" method="POST">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger">Permanently Delete</button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="text-center">No deleted documents found.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-center">
        {{ $documents->links() }}
      </div>
    </div>
  </div>
</div>
@endsection