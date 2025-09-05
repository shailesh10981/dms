@extends('components.app')

@php
use App\Models\Document;
use App\Models\User;
@endphp

@section('title', 'Audit Logs')

@section('content')
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-md-6">
      <h2>Audit Logs</h2>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <form method="GET" action="{{ route('audit-logs.index') }}" id="filter-form">
        <div class="row">
          <div class="col-md-3">
            <select name="document_id" class="form-control form-control-sm" onchange="document.getElementById('filter-form').submit()">
              <option value="">All Documents</option>
              @foreach(Document::all() as $doc)
              <option value="{{ $doc->id }}" {{ request('document_id') == $doc->id ? 'selected' : '' }}>
                {{ $doc->document_id }} - {{ $doc->title }}
              </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <select name="user_id" class="form-control form-control-sm" onchange="document.getElementById('filter-form').submit()">
              <option value="">All Users</option>
              @foreach(User::all() as $user)
              <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                {{ $user->name }}
              </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <select name="action" class="form-control form-control-sm" onchange="document.getElementById('filter-form').submit()">
              <option value="">All Actions</option>
              @foreach(['upload', 'update', 'submit', 'approve', 'reject', 'download', 'preview', 'delete', 'restore'] as $action)
              <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                {{ ucfirst($action) }}
              </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <div class="input-group input-group-sm">
              <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="From">
              <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="To">
              <div class="input-group-append">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-filter"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead class="thead-light">
            <tr>
              <th>Timestamp</th>
              <th>Document</th>
              <th>User</th>
              <th>Action</th>
              <th>Details</th>
              <th>IP Address</th>
            </tr>
          </thead>
          <tbody>
            @forelse($logs as $log)
            <tr>
              <td>{{ $log->created_at->format('M d, Y H:i:s') }}</td>
              <td>
                @if($log->document)
                <a href="{{ route('documents.show', $log->document_id) }}">
                  {{ $log->document->document_id }}
                </a>
                @else
                N/A
                @endif
              </td>
              <td>{{ $log->user->name ?? 'System' }}</td>
              <td>{{ ucfirst($log->action) }}</td>
              <td>{{ $log->details }}</td>
              <td>{{ $log->ip_address }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="text-center">No audit logs found.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-center">
        {{ $logs->appends(request()->query())->links() }}
      </div>
    </div>
  </div>
</div>
@endsection