@extends('components.app')

@section('title', 'Compliance Reports')

@section('content')
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-md-6">
      <h2>Compliance Reports</h2>
    </div>
    <div class="col-md-6 text-right">
      @can('create', App\Models\ComplianceReport::class)
      <a href="{{ route('compliance.reports.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> New Report
      </a>
      @endcan
    </div>
  </div>
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('compliance.reports.index') }}">
        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label>Status</label>
              <select name="status" class="form-control">
                <option value="">All Statuses</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
              </select>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label>Template</label>
              <select name="template_id" class="form-control">
                <option value="">All Templates</option>
                @foreach($templates as $id => $name)
                <option value="{{ $id }}" {{ request('template_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label>Department</label>
              <select name="department_id" class="form-control">
                <option value="">All Departments</option>
                @foreach($departments as $id => $name)
                <option value="{{ $id }}" {{ request('department_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('compliance.reports.index') }}" class="btn btn-link ml-2">Reset</a>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="card">
    <div class="card-header">
      <div class="row">
        <div class="col-md-6">
          <div class="input-group">
            <input type="text" class="form-control" placeholder="Search reports...">
            <div class="input-group-append">
              <button class="btn btn-outline-secondary" type="button">
                <i class="fas fa-search"></i>
              </button>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="float-right">
            <div class="btn-group">
              <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Filter by Status
              </button>
              <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="#">All</a>
                <a class="dropdown-item" href="#">Draft</a>
                <a class="dropdown-item" href="#">Submitted</a>
                <a class="dropdown-item" href="#">Approved</a>
                <a class="dropdown-item" href="#">Rejected</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead class="thead-light">
            <tr>
              <th>Report ID</th>
              <th>Title</th>
              <th>Template</th>
              <th>Department</th>
              <th>Status</th>
              <th>Created</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($reports as $report)
            <tr>
              <td>{{ $report->report_id }}</td>
              <td>{{ $report->title }}</td>
              <td>{{ $report->template->name }}</td>
              <td>{{ $report->department->name }}</td>
              <td>
                <span class="badge 
                                        @if($report->status === 'draft') bg-secondary
                                        @elseif($report->status === 'submitted') bg-primary
                                        @elseif($report->status === 'approved') bg-success
                                        @elseif($report->status === 'rejected') bg-danger @endif">
                  {{ ucfirst($report->status) }}
                </span>
              </td>
              <td>{{ $report->created_at->format('Y-m-d') }}</td>
              <td>
                <div class="btn-group btn-group-sm" role="group">
                  <a href="{{ route('compliance.reports.show', $report) }}"
                    class="btn btn-info" title="View">
                    <i class="fas fa-eye"></i>
                  </a>
                  @can('update', $report)
                  <a href="{{ route('compliance.reports.edit', $report) }}"
                    class="btn btn-primary" title="Edit">
                    <i class="fas fa-edit"></i>
                  </a>
                  @endcan
                  @can('delete', $report)
                  <form action="{{ route('compliance.reports.destroy', $report) }}"
                    method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"
                      title="Delete" onclick="return confirm('Are you sure?')">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                  @endcan
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="text-center">No reports found</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-center">
        {{ $reports->links() }}
      </div>
    </div>
  </div>
</div>
@endsection