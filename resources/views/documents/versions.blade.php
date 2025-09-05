@extends('components.app')

@section('title', 'Document Versions')

@section('content')
<div class="container-fluid">
  <div class="row mb-4">
    <div class="col-md-6">
      <h2>Version History: {{ $document->title }}</h2>
    </div>
    <div class="col-md-6 text-right">
      <a href="{{ route('documents.show', $document->id) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Document
      </a>
      @can('document_upload')
      <a href="{{ route('documents.create-version', $document->id) }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Create New Version
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
              <th>Version</th>
              <th>Document ID</th>
              <th>Title</th>
              <th>Status</th>
              <th>Uploaded By</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($document->version_chain as $version)
            <tr class="{{ $version->id == $document->id ? 'table-active' : '' }}">
              <td>v{{ $version->version }}</td>
              <td>{{ $version->document_id }}</td>
              <td>{{ $version->title }}</td>
              <td>{!! $version->status_badge !!}</td>
              <td>{{ $version->uploader->name }}</td>
              <td>{{ $version->created_at->format('M d, Y H:i') }}</td>
              <td>
                <div class="btn-group btn-group-sm" role="group">
                  <a href="{{ route('documents.show', $version->id) }}" class="btn btn-info" title="View">
                    <i class="fas fa-eye"></i>
                  </a>
                  @can('document_download')
                  <a href="{{ route('documents.download', $version->id) }}" class="btn btn-secondary" title="Download">
                    <i class="fas fa-download"></i>
                  </a>
                  @endcan
                  @if($version->status == 'draft' && $version->uploaded_by == auth()->id())
                  <a href="{{ route('documents.edit', $version->id) }}" class="btn btn-primary" title="Edit">
                    <i class="fas fa-edit"></i>
                  </a>
                  @endif
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection