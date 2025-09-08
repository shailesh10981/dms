@extends('components.app')
@section('title', 'Edit Risk Report')
@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('risk.reports.update', $report) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
          <label class="form-label">Title</label>
          <input type="text" class="form-control" name="title" value="{{ $report->title }}" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea class="form-control" name="description" rows="3">{{ $report->description }}</textarea>
        </div>
        @foreach($fields as $f)
          <div class="mb-3">
            <label class="form-label text-capitalize">{{ str_replace('_',' ', $f) }}</label>
            <input class="form-control" name="data[{{ $f }}]" value="{{ $report->data[$f] ?? '' }}" />
          </div>
        @endforeach
        <button type="submit" class="btn btn-primary">Save</button>
      </form>
    </div>
  </div>
</div>
@endsection
