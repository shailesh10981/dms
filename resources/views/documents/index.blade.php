@extends('components.app')

@section('title', 'Documents')

@section('content')
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-md-6">
      <h2>Documents</h2>
    </div>
    <div class="col-md-6 text-right">
      @can('document_upload')
      <a href="{{ route('documents.create') }}" class="btn btn-primary">
        <i class="fas fa-upload"></i> Upload Document
      </a>
      @endcan
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <div class="row">
        <div class="col-md-6">
          <form method="GET" action="{{ route('documents.index') }}" class="form-inline">
            <div class="input-group">
              <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
              <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="submit">
                  <i class="fas fa-search"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
        <div class="col-md-6">
          <form method="GET" action="{{ route('documents.index') }}" id="filter-form">
            <div class="row">
              <div class="col">
                <select name="department_id" class="form-control form-control-sm" onchange="document.getElementById('filter-form').submit()">
                  <option value="">All Departments</option>
                  @foreach($departments as $department)
                  <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                    {{ $department->name }}
                  </option>
                  @endforeach
                </select>
              </div>
              <div class="col">
                <select name="location_id" class="form-control form-control-sm" onchange="document.getElementById('filter-form').submit()">
                  <option value="">All Locations</option>
                  @foreach($locations as $location)
                  <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                    {{ $location->name }}
                  </option>
                  @endforeach
                </select>
              </div>
              <div class="col">
                <select name="status" class="form-control form-control-sm" onchange="document.getElementById('filter-form').submit()">
                  <option value="">All Statuses</option>
                  <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                  <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                  <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                  <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead class="thead-light">
            <tr>
              <th>Document ID</th>
              <th>Title</th>
              <th>Department</th>
              <th>Status</th>
              <th>Uploaded By</th>
              <th>Upload Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($documents as $document)
            <tr>
              <td>{{ $document->document_id }}</td>
              <td>{{ $document->title }}</td>
              <td>{{ $document->department->name }}</td>
              <td>{!! $document->latestVersion->status_badge !!}</td>
              <td>{{ $document->uploader->name }}</td>
              <td>{{ $document->created_at->format('M d, Y H:i') }}</td>
              <td>
                <div class="btn-group btn-group-sm" role="group">
                  <a href="{{ route('documents.show', $document->id) }}" class="btn btn-info" title="View">
                    <i class="fas fa-eye"></i>
                  </a>
                  @can('document_download')
                  <a href="{{ route('documents.download', $document->id) }}" class="btn btn-secondary" title="Download">
                    <i class="fas fa-download"></i>
                  </a>
                  @endcan
                  @if($document->status == 'draft' && $document->uploaded_by == auth()->id())
                  <a href="{{ route('documents.edit', $document->id) }}" class="btn btn-primary" title="Edit">
                    <i class="fas fa-edit"></i>
                  </a>
                  <form action="{{ route('documents.destroy', $document->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this document?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" title="Delete">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                  @endif
                  @if($document->status == 'draft' && $document->uploaded_by == auth()->id())
                  <form action="{{ route('documents.submit', $document->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-warning" title="Submit for Approval">
                      <i class="fas fa-paper-plane"></i>
                    </button>
                  </form>
                  @endif
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="text-center">No documents found.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-center">
        {{ $documents->appends(request()->query())->links() }}
      </div>
    </div>
  </div>
</div>
@endsection