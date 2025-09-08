@extends('components.app')

@section('title', 'Document Details')

@section('content')
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-md-6">
      <h2>Document Details</h2>
    </div>
    <div class="col-md-6 text-right">
      <a href="{{ route('documents.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Documents
      </a>
    </div>
  </div>

  <div class="row">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">{{ $document->title }}</h3>
          <div class="card-tools">
            <span class="badge badge-secondary">{{ $document->document_id }}</span>
            {!! $document->status_badge !!}
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <p><strong>Department:</strong> {{ $document->department->name }}</p>
              <p><strong>Location:</strong> {{ $document->location ? $document->location->name : 'N/A' }}</p>
              <p><strong>Project:</strong> {{ $document->project ? $document->project->name : 'N/A' }}</p>
              <p><strong>Visibility:</strong> {{ $document->visibility ?? 'Private' }}</p>
            </div>
            <div class="col-md-6">
              <p><strong>Uploaded By:</strong> {{ $document->uploader->name }}</p>
              <p><strong>Upload Date:</strong> {{ $document->created_at->format('M d, Y H:i') }}</p>
              <p><strong>Expiry Date:</strong> {{ $document->expiry_date ? $document->expiry_date->format('M d, Y') : 'N/A' }}</p>
            </div>
          </div>

          <div class="mt-3">
            <p><strong>Description:</strong></p>
            <p>{{ $document->description ?: 'No description provided.' }}</p>
          </div>

          <div class="mt-4">
            <p><strong>File Information:</strong></p>
            <div class="alert alert-info">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <i class="fas fa-file mr-2"></i> {{ $document->file_name }}
                  <small class="text-muted ml-2">({{ $document->file_type }}, {{ number_format($document->file_size / 1024, 2) }} KB)</small>
                </div>
                <div>
                  @can('document_download')
                  <a href="{{ route('documents.download', $document->id) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-download"></i> Download
                  </a>
                  @endcan
                  @can('document_view')
                  <a href="{{ route('documents.preview', $document->id) }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-eye"></i> Preview
                  </a>
                  @endcan
                </div>
              </div>
            </div>
          </div>

          @if($document->status == 'rejected')
          <div class="alert alert-danger mt-3">
            <h5><i class="fas fa-times-circle"></i> Rejection Details</h5>
            <p><strong>Reason:</strong> {{ $document->rejection_reason }}</p>
            <p><strong>Rejected By:</strong> {{ $document->approver->name }}</p>
            <p><strong>Rejected At:</strong> {{ $document->updated_at->format('M d, Y H:i') }}</p>
          </div>
          @endif

          @if($document->status == 'approved')
          <div class="alert alert-success mt-3">
            <h5><i class="fas fa-check-circle"></i> Approval Details</h5>
            <p><strong>Approved By:</strong> {{ $document->approver->name }}</p>
            <p><strong>Approved At:</strong> {{ $document->updated_at->format('M d, Y H:i') }}</p>
          </div>
          @endif
        </div>
        <div class="card-footer">
          <div class="d-flex justify-content-between">
            <div>
              @can('update', $document)
              <a href="{{ route('documents.edit', $document->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
              </a>
              @endcan

              @can('delete', $document)
              <form action="{{ route('documents.destroy', $document->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                  <i class="fas fa-trash"></i> Delete
                </button>
              </form>
              @endcan

              @can('submitForApproval', $document)
              <form action="{{ route('documents.submit', $document->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-warning">
                  <i class="fas fa-paper-plane"></i> Submit for Approval
                </button>
              </form>
              @endcan
            </div>

            <div>
              @can('approve', $document)
              <form action="{{ route('approvals.approve', $document->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success">
                  <i class="fas fa-check"></i> Approve
                </button>
              </form>

              <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                <i class="fas fa-times"></i> Reject
              </button>
              @endcan
            </div>
          </div>
        </div>
      </div>

      @if(in_array($document->status, ['submitted', 'resubmitted']) && $document->current_approver_id == auth()->id())
      <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="rejectModalLabel">Reject Document</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('approvals.reject', $document->id) }}" method="POST">
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
      @endif
    </div>

    <div class="col-md-4">
      <!-- Enhanced Version History -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Version History</h3>
        </div>
        <div class="card-body p-0">
          <ul class="timeline">
            @foreach($versions as $version)
            <li class="{{ $version->id == $document->id ? 'current-version' : '' }}">
              <div class="timeline-item">
                <div class="timeline-header">
                  <span class="version-badge">v{{ $version->version }}</span>
                  <span class="document-id">{{ $version->document_id }}</span>
                  <span class="status-badge {{ $version->status }}">{{ ucfirst($version->status) }}</span>
                </div>

                @if($version->status == 'rejected')
                <div class="rejection-reason">
                  <i class="fas fa-times-circle"></i>
                  <strong>Reason:</strong> {{ $version->rejection_reason }}
                </div>
                @endif

                <div class="timeline-content">
                  <p class="mb-1"><strong>{{ $version->title }}</strong></p>
                  <p class="text-muted small mb-1">
                    <i class="fas fa-user"></i> {{ $version->uploader->name }}
                    | <i class="fas fa-clock"></i> {{ $version->created_at->format('M d, Y H:i') }}
                  </p>
                </div>

                <div class="timeline-actions">
                  <a href="{{ route('documents.show', $version->id) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-eye"></i> View
                  </a>
                  @can('document_download')
                  <a href="{{ route('documents.download', $version->id) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-download"></i> Download
                  </a>
                  @endcan
                </div>
              </div>
            </li>
            @endforeach
          </ul>
        </div>
      </div>

      <!-- Enhanced Audit Log -->
      <div class="card mt-4">
        <div class="card-header">
          <h3 class="card-title">Document Activity</h3>
        </div>
        <div class="card-body p-0">
          <ul class="audit-log">
            @foreach($document->auditLogs as $log)
            <li class="log-entry">
              <div class="log-header">
                <span class="log-action">{{ ucfirst($log->action) }}</span>
                <span class="log-date">{{ $log->created_at->format('M d, Y H:i') }}</span>
                <span class="log-user">{{ $log->user->name }}</span>
              </div>

              <div class="log-details">
                @if(is_array($log->details))
                @foreach($log->details as $key => $value)
                @if(!empty($value))
                <div>
                  <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                  @if(is_array($value))
                  {{ json_encode($value) }}
                  @else
                  {{ $value }}
                  @endif
                </div>
                @endif
                @endforeach
                @else
                {{ $log->details }}
                @endif
              </div>
            </li>
            @endforeach
          </ul>
        </div>
      </div>

      <!-- Version Control Actions -->
      <div class="card mt-4">
        <div class="card-header">
          <h3 class="card-title">Version Actions</h3>
        </div>
        <div class="card-body">
          @can('document_upload')
          <a href="{{ route('documents.create-version', $document->id) }}" class="btn btn-primary btn-block">
            <i class="fas fa-plus"></i> Create New Version
          </a>
          @endcan

          @if($document->parent)
          <a href="{{ route('documents.show', $document->parent->id) }}" class="btn btn-outline-secondary btn-block mt-2">
            <i class="fas fa-arrow-up"></i> View Parent Version
          </a>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  /* Timeline Styles */
  .timeline {
    list-style: none;
    padding: 0;
    position: relative;
  }

  .timeline:before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
    left: 20px;
    margin-left: -1px;
  }

  .timeline li {
    position: relative;
    padding-left: 40px;
    margin-bottom: 15px;
  }

  .timeline li:last-child {
    margin-bottom: 0;
  }

  .timeline li:before {
    content: '';
    position: absolute;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #6c757d;
    left: 14px;
    top: 4px;
  }

  .timeline li.current-version:before {
    background: #28a745;
    width: 14px;
    height: 14px;
    left: 13px;
    top: 3px;
  }

  .timeline-item {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    padding: 15px;
  }

  .timeline-header {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
  }

  .version-badge {
    background: #6c757d;
    color: white;
    padding: 2px 6px;
    border-radius: 3px;
    font-weight: bold;
    font-size: 0.8em;
    margin-right: 8px;
  }

  .status-badge {
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 0.8em;
    margin-left: auto;
  }

  .status-badge.approved {
    background: #28a745;
    color: white;
  }

  .status-badge.rejected {
    background: #dc3545;
    color: white;
  }

  .status-badge.draft {
    background: #ffc107;
    color: black;
  }

  .status-badge.submitted {
    background: #17a2b8;
    color: white;
  }

  .rejection-reason {
    color: #dc3545;
    font-size: 0.9em;
    margin: 5px 0;
    padding: 5px;
    background: rgba(220, 53, 69, 0.1);
    border-radius: 3px;
  }

  .timeline-actions {
    margin-top: 10px;
    display: flex;
    gap: 5px;
  }

  /* Audit Log Styles */
  .audit-log {
    list-style: none;
    padding: 0;
  }

  .log-entry {
    padding: 12px 15px;
    border-bottom: 1px solid #e9ecef;
  }

  .log-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
  }

  .log-action {
    font-weight: bold;
    color: #495057;
  }

  .log-date {
    color: #6c757d;
    font-size: 0.85em;
  }

  .log-user {
    color: #6c757d;
    font-size: 0.85em;
  }

  .log-details {
    font-size: 0.9em;
    color: #495057;
    padding-left: 10px;
    border-left: 2px solid #dee2e6;
  }
</style>
@endsection