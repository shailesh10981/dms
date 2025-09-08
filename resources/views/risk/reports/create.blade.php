@extends('components.app')
@section('title', 'Create Risk Report')
@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('risk.reports.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
          <label class="form-label">Issue Type</label>
          <select name="issue_type" id="issue_type" class="form-select" required>
            <option value="">Select</option>
            <option value="operational">Operational Risk</option>
            <option value="compliance">Compliance Risk</option>
            <option value="financial">Financial Risk</option>
            <option value="security">Security Risk</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Title</label>
          <input type="text" class="form-control" name="title" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Department</label>
          <select name="department_id" class="form-select" required>
            @foreach($departments as $id => $name)
              <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea class="form-control" name="description" rows="3"></textarea>
        </div>

        <div id="dynamic-fields"></div>

        <div class="mb-3">
          <label class="form-label">Attachment (optional)</label>
          <input type="file" class="form-control" name="attachment" />
        </div>

        <div class="mb-3">
          <label class="form-label">Approval Workflow (select approvers in order)</label>
          <select name="approver_ids[]" class="form-select select2" multiple data-placeholder="Select approvers in order">
            @foreach($approvers as $u)
              <option value="{{ $u->id }}">{{ $u->name }} @if($u->department) ({{ $u->department->code }}) @endif</option>
            @endforeach
          </select>
          <small class="text-muted">HOD will be preselected if available; you can override order.</small>
        </div>

        <button type="submit" class="btn btn-secondary" name="save">Save Draft</button>
        <button type="submit" class="btn btn-primary" name="submit" value="1">Submit</button>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
const fieldsByType = @json($fieldsByType);
const managersByDept = @json($managersByDept ?? []);
const container = document.getElementById('dynamic-fields');
document.getElementById('issue_type').addEventListener('change', function() {
  const type = this.value;
  container.innerHTML = '';
  (fieldsByType[type]||[]).forEach(f => {
    const div = document.createElement('div');
    div.className = 'mb-3';
    div.innerHTML = `<label class="form-label text-capitalize">${f.replace('_',' ')}</label>`+
                    `<input class="form-control" name="data[${f}]" />`;
    container.appendChild(div);
  })
});

// Preselect HOD (manager) when department changes
const deptSelect = document.querySelector('select[name="department_id"]');
const approverSelect = $('select[name="approver_ids[]"]');
if (deptSelect) {
  deptSelect.addEventListener('change', function() {
    const hodId = managersByDept[this.value];
    if (hodId) {
      let vals = approverSelect.val() || [];
      if (!vals.includes(String(hodId))) {
        vals.unshift(String(hodId));
        approverSelect.val(vals).trigger('change');
      }
    }
  });
}
// trigger preselect if department already chosen
if (deptSelect && deptSelect.value) {
  const event = new Event('change');
  deptSelect.dispatchEvent(event);
}
</script>
@endpush
@endsection
