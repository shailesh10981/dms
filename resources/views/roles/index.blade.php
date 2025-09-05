@extends('components.app')

@section('title', 'Role Management')

@section('content')
<div class="container-fluid">
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1>Role Management</h1>
    </div>
    <div class="col-sm-6 text-end">
      @can('role_create')
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
        <i class="fas fa-plus"></i> Add New Role
      </button>
      @endcan
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Permissions</th>
          <th>Users</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($roles as $role)
        <tr>
          <td>{{ $role->id }}</td>
          <td>{{ $role->name }}</td>
          <td>
            @foreach($role->permissions as $permission)
            <span class="badge bg-info text-dark">{{ $permission->name }}</span>
            @endforeach
          </td>
          <td>{{ $role->users_count ?? $role->users->count() }}</td>
          <td>
            @can('role_edit')
            <button class="btn btn-sm btn-primary edit-role"
              data-id="{{ $role->id }}"
              data-name="{{ $role->name }}"
              data-permissions="{{ implode(',', $role->permissions->pluck('id')->toArray()) }}">
              <i class="fas fa-edit"></i>
            </button>
            @endcan

            @can('role_delete')
            @if(!in_array($role->name, ['admin']))
            <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                <i class="fas fa-trash"></i>
              </button>
            </form>
            @endif
            @endcan
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<!-- Create Role Modal -->
<div class="modal fade" id="createRoleModal" tabindex="-1" aria-labelledby="createRoleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('admin.roles.store') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="createRoleModalLabel">Create New Role</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="form-group mb-3">
            <label for="name">Role Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
          </div>

          <div class="form-group mb-3">
            <label>Permissions</label>
            <div class="permission-container" style="max-height: 300px; overflow-y: auto;">
              @php
              $groupedPermissions = $permissions->groupBy('module');
              @endphp
              @foreach($groupedPermissions as $module => $modulePermissions)
              <div class="card mb-2">
                <div class="card-header py-1">
                  <h6 class="mb-0">{{ ucfirst($module) }}</h6>
                </div>
                <div class="card-body py-2">
                  @foreach($modulePermissions as $permission)
                  <div class="form-check">
                    <input type="checkbox" name="permissions[]" id="perm_{{ $permission->id }}"
                      value="{{ $permission->id }}" class="form-check-input">
                    <label for="perm_{{ $permission->id }}" class="form-check-label">
                      {{ $permission->name }}
                    </label>
                  </div>
                  @endforeach
                </div>
              </div>
              @endforeach
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Create Role</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="" id="editRoleForm">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title" id="editRoleModalLabel">Edit Role</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="form-group mb-3">
            <label for="edit_name">Role Name</label>
            <input type="text" name="name" id="edit_name" class="form-control" required>
          </div>

          <div class="form-group mb-3">
            <label>Permissions</label>
            <div class="permission-container" style="max-height: 300px; overflow-y: auto;">
              @foreach($permissions->groupBy('module') as $module => $modulePermissions)
              <div class="card mb-2">
                <div class="card-header py-1">
                  <h6 class="mb-0">{{ ucfirst($module) }}</h6>
                </div>
                <div class="card-body py-2">
                  @foreach($modulePermissions as $permission)
                  <div class="form-check">
                    <input type="checkbox" name="permissions[]" id="edit_perm_{{ $permission->id }}"
                      value="{{ $permission->id }}" class="form-check-input">
                    <label for="edit_perm_{{ $permission->id }}" class="form-check-label">
                      {{ $permission->name }}
                    </label>
                  </div>
                  @endforeach
                </div>
              </div>
              @endforeach
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Update Role</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  $(document).ready(function() {
    $('.edit-role').click(function() {
      const roleId = $(this).data('id');
      const roleName = $(this).data('name');
      const permissionsStr = $(this).data('permissions') || '';
      const rolePermissions = permissionsStr.toString().split(',').map(id => parseInt(id));

      $('#editRoleForm').attr('action', '/admin/roles/' + roleId);
      $('#edit_name').val(roleName);

      // Uncheck all
      $('#editRoleModal input[type="checkbox"]').prop('checked', false);

      // Check selected permissions
      rolePermissions.forEach(function(permissionId) {
        $('#edit_perm_' + permissionId).prop('checked', true);
      });

      const modal = new bootstrap.Modal(document.getElementById('editRoleModal'));
      modal.show();
    });

    $('#editRoleForm').submit(function(e) {
      e.preventDefault();
      const form = $(this);

      $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: form.serialize(),
        success: function() {
          const modal = bootstrap.Modal.getInstance(document.getElementById('editRoleModal'));
          modal.hide();
          location.reload();
        },
        error: function(xhr) {
          alert('Error: ' + xhr.responseText);
        }
      });
    });
  });
</script>
@endpush