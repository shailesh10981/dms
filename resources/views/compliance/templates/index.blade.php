@extends('components.app')

@section('title', 'Compliance Templates')

@section('content')
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-md-6">
      <h2>Compliance Templates</h2>
    </div>
    <div class="col-md-6 text-right">
      @can('create', App\Models\ComplianceTemplate::class)
      <a href="{{ route('compliance.templates.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> New Template
      </a>
      @endcan
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead class="thead-light">
            <tr>
              <th>Name</th>
              <th>Department</th>
              <th>Frequency</th>
              <th>Fields</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($templates as $template)
            <tr>
              <td>{{ $template->name }}</td>
              <td>{{ $template->department->name }}</td>
              <td>{{ ucfirst($template->frequency) }}</td>
              <td>{{ $template->fields->count() }}</td>
              <td>
                @if($template->is_active)
                <span class="badge bg-success">Active</span>
                @else
                <span class="badge bg-secondary">Inactive</span>
                @endif
              </td>
              <td>
                <div class="btn-group btn-group-sm" role="group">
                  <a href="{{ route('compliance.templates.show', $template->id) }}"
                    class="btn btn-info" title="View">
                    <i class="fas fa-eye"></i>
                  </a>
                  @can('update', $template)
                  <a href="{{ route('compliance.templates.edit', $template->id) }}"
                    class="btn btn-primary" title="Edit">
                    <i class="fas fa-edit"></i>
                  </a>
                  @endcan
                  @can('delete', $template)
                  <form action="{{ route('compliance.templates.destroy', $template->id) }}"
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
              <td colspan="6" class="text-center">No templates found</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-center">
        {{ $templates->links() }}
      </div>
    </div>
  </div>
</div>
@endsection