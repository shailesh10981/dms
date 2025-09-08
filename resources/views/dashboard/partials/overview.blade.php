<div class="row">
  <div class="col-lg-3 col-6">
    <div class="small-box bg-primary">
      <div class="inner">
        <h3>{{ $totalDocuments }}</h3>
        <p>Total Documents</p>
      </div>
      <div class="icon"><i class="fas fa-file"></i></div>
      <a href="{{ route('documents.index') }}" class="small-box-footer">View <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="small-box bg-success">
      <div class="inner">
        <h3>{{ $publicDocuments }}</h3>
        <p>Public</p>
      </div>
      <div class="icon"><i class="fas fa-globe"></i></div>
      <a href="{{ route('documents.index') }}?visibility=Public" class="small-box-footer">Browse <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="small-box bg-info">
      <div class="inner">
        <h3>{{ $publishDocuments }}</h3>
        <p>Publish</p>
      </div>
      <div class="icon"><i class="fas fa-bullhorn"></i></div>
      <a href="{{ route('documents.index') }}?visibility=Publish" class="small-box-footer">Browse <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="small-box bg-warning">
      <div class="inner">
        <h3>{{ $privateDocuments }}</h3>
        <p>Private</p>
      </div>
      <div class="icon"><i class="fas fa-lock"></i></div>
      <a href="{{ route('documents.index') }}?visibility=Private" class="small-box-footer">Browse <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Quick Actions</h3>
        <div>
          <a href="{{ route('documents.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-upload"></i> Upload Document</a>
          <a href="{{ route('risk.reports.create') }}" class="btn btn-sm btn-warning"><i class="fas fa-exclamation-triangle"></i> Create Risk</a>
        </div>
      </div>
      <div class="card-body">
        <p class="text-muted mb-0">Use quick actions to start new submissions.</p>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h3 class="card-title">Pending Approvals (Documents)</h3></div>
      <div class="card-body p-0">
        <ul class="list-group list-group-flush">
          @forelse($pendingDocApprovals as $ap)
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span>
                <strong>{{ $ap->document->title }}</strong>
                <small class="text-muted d-block">{{ $ap->document->department->name ?? '—' }} • {{ $ap->created_at }}</small>
              </span>
              <a href="{{ route('documents.show', $ap->document_id) }}" class="btn btn-sm btn-outline-primary">Review</a>
            </li>
          @empty
            <li class="list-group-item text-muted">No pending approvals.</li>
          @endforelse
        </ul>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card">
      <div class="card-header"><h3 class="card-title">Pending Approvals (Risks)</h3></div>
      <div class="card-body p-0">
        <ul class="list-group list-group-flush">
          @forelse($pendingRisk as $r)
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span>
                <strong>{{ $r->title }}</strong>
                <small class="text-muted d-block">{{ ucfirst($r->issue_type) }} • {{ $r->department->name ?? '—' }} • {{ $r->created_at }}</small>
              </span>
              <a href="{{ route('risk.reports.show', $r) }}" class="btn btn-sm btn-outline-primary">Review</a>
            </li>
          @empty
            <li class="list-group-item text-muted">No pending approvals.</li>
          @endforelse
        </ul>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h3 class="card-title">Recent Documents</h3></div>
      <div class="card-body p-0">
        <ul class="list-group list-group-flush">
          @forelse($recentDocuments as $d)
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span>
                <strong>{{ $d->title }}</strong>
                <small class="text-muted d-block">{{ $d->department->name ?? '—' }} • {{ $d->created_at }}</small>
              </span>
              <a href="{{ route('documents.show', $d) }}" class="btn btn-sm btn-outline-secondary">Open</a>
            </li>
          @empty
            <li class="list-group-item text-muted">No recent documents.</li>
          @endforelse
        </ul>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h3 class="card-title">Recent Risks</h3></div>
      <div class="card-body p-0">
        <ul class="list-group list-group-flush">
          @forelse($recentRisks as $r)
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span>
                <strong>{{ $r->title }}</strong>
                <small class="text-muted d-block">{{ ucfirst($r->issue_type) }} • {{ $r->department->name ?? '—' }} • {{ $r->created_at }}</small>
              </span>
              <a href="{{ route('risk.reports.show', $r) }}" class="btn btn-sm btn-outline-secondary">Open</a>
            </li>
          @empty
            <li class="list-group-item text-muted">No recent risks.</li>
          @endforelse
        </ul>
      </div>
    </div>
  </div>
</div>
