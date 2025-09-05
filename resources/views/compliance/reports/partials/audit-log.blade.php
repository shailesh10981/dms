<div class="card mt-4">
  <div class="card-header">
    <h5>Audit Trail</h5>
  </div>
  <div class="card-body">
    <div class="timeline">
      @foreach($report->auditLogs as $log)
      <div class="timeline-item">
        <div class="timeline-item-marker">
          <div class="timeline-item-marker-indicator bg-{{ 
                        $log->action === 'approved' ? 'success' : 
                        ($log->action === 'rejected' ? 'danger' : 'primary') 
                    }}"></div>
        </div>
        <div class="timeline-item-content">
          <div class="d-flex justify-content-between">
            <strong>{{ ucfirst($log->action) }}</strong>
            <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
          </div>
          <p class="mb-1">{{ $log->user->name }}</p>
          @if($log->comments)
          <div class="alert alert-light p-2 mt-2">
            {{ $log->comments }}
          </div>
          @endif
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>