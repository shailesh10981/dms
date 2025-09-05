@extends('components.app')

@section('title', 'System Settings')

@section('content')
<div class="container-fluid">
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1>System Settings</h1>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('admin.settings.update') }}">
      @csrf

      <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" id="general-tab" data-bs-toggle="tab" href="#general" role="tab">General</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="mail-tab" data-bs-toggle="tab" href="#mail" role="tab">Mail</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="document-tab" data-bs-toggle="tab" href="#document" role="tab">Documents</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="compliance-tab" data-bs-toggle="tab" href="#compliance" role="tab">Compliance</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="notifications-tab" data-bs-toggle="tab" href="#notifications" role="tab">Notifications</a>
        </li>
      </ul>

      <div class="tab-content pt-3" id="settingsTabsContent">
        <!-- General Settings -->
        <div class="tab-pane fade show active" id="general" role="tabpanel">
          <div class="form-group">
            <label for="app_name">Application Name</label>
            <input type="text" name="general[app_name]" id="app_name"
              class="form-control" value="{{ $settings['general']['app_name'] }}">
          </div>

          <div class="form-group">
            <label for="timezone">Timezone</label>
            <select name="general[timezone]" id="timezone" class="form-control select2">
              @foreach(timezone_identifiers_list() as $tz)
              <option value="{{ $tz }}" {{ $settings['general']['timezone'] == $tz ? 'selected' : '' }}>
                {{ $tz }}
              </option>
              @endforeach
            </select>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="date_format">Date Format</label>
                <select name="general[date_format]" id="date_format" class="form-control">
                  <option value="Y-m-d" {{ $settings['general']['date_format'] == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                  <option value="d/m/Y" {{ $settings['general']['date_format'] == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                  <option value="m/d/Y" {{ $settings['general']['date_format'] == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="time_format">Time Format</label>
                <select name="general[time_format]" id="time_format" class="form-control">
                  <option value="H:i:s" {{ $settings['general']['time_format'] == 'H:i:s' ? 'selected' : '' }}>24-hour (14:30:00)</option>
                  <option value="h:i:s A" {{ $settings['general']['time_format'] == 'h:i:s A' ? 'selected' : '' }}>12-hour (02:30:00 PM)</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Mail Settings -->
        <div class="tab-pane fade" id="mail" role="tabpanel">
          <div class="form-group">
            <label for="mail_from_name">From Name</label>
            <input type="text" name="mail[mail_from_name]" id="mail_from_name"
              class="form-control" value="{{ $settings['mail']['mail_from_name'] }}">
          </div>

          <div class="form-group">
            <label for="mail_from_address">From Address</label>
            <input type="email" name="mail[mail_from_address]" id="mail_from_address"
              class="form-control" value="{{ $settings['mail']['mail_from_address'] }}">
          </div>
        </div>

        <!-- Document Settings -->
        <div class="tab-pane fade" id="document" role="tabpanel">
          <div class="form-group">
            <label for="document_retention_days">Document Retention Period (days)</label>
            <input type="number" name="document[document_retention_days]" id="document_retention_days"
              class="form-control" value="{{ $settings['document']['document_retention_days'] }}" min="1">
          </div>

          <div class="form-group">
            <label for="max_file_size">Maximum File Size (MB)</label>
            <input type="number" name="document[max_file_size]" id="max_file_size"
              class="form-control" value="{{ $settings['document']['max_file_size'] }}" min="1" max="50">
          </div>

          <div class="form-group">
            <label for="allowed_file_types">Allowed File Types</label>
            <select name="document[allowed_file_types][]" id="allowed_file_types"
              class="form-control select2" multiple>
              @php
              $allowedTypes = $settings['document']['allowed_file_types'];
              @endphp
              <option value="pdf" {{ in_array('pdf', $allowedTypes) ? 'selected' : '' }}>PDF</option>
              <option value="doc" {{ in_array('doc', $allowedTypes) ? 'selected' : '' }}>DOC</option>
              <option value="docx" {{ in_array('docx', $allowedTypes) ? 'selected' : '' }}>DOCX</option>
              <option value="xls" {{ in_array('xls', $allowedTypes) ? 'selected' : '' }}>XLS</option>
              <option value="xlsx" {{ in_array('xlsx', $allowedTypes) ? 'selected' : '' }}>XLSX</option>
              <option value="jpg" {{ in_array('jpg', $allowedTypes) ? 'selected' : '' }}>JPG</option>
              <option value="png" {{ in_array('png', $allowedTypes) ? 'selected' : '' }}>PNG</option>
              <option value="txt" {{ in_array('txt', $allowedTypes) ? 'selected' : '' }}>TXT</option>
            </select>
          </div>
        </div>

        <!-- Compliance Settings -->
        <div class="tab-pane fade" id="compliance" role="tabpanel">
          <div class="form-group">
            <label for="default_due_days">Default Due Days</label>
            <input type="number" name="compliance[default_due_days]" id="default_due_days"
              class="form-control" value="{{ $settings['compliance']['default_due_days'] }}" min="1">
            <small class="text-muted">Default number of days before a compliance report is due</small>
          </div>

          <div class="form-group">
            <label for="reminder_days_before">Reminder Days Before Due</label>
            <input type="number" name="compliance[reminder_days_before]" id="reminder_days_before"
              class="form-control" value="{{ $settings['compliance']['reminder_days_before'] }}" min="1">
            <small class="text-muted">Number of days before due date to send reminders</small>
          </div>
        </div>

        <!-- Notification Settings -->
        <div class="tab-pane fade" id="notifications" role="tabpanel">
          <div class="form-check">
            <input type="checkbox" name="notifications[enable_email]" id="enable_email"
              class="form-check-input" value="1" {{ $settings['notifications']['enable_email'] ? 'checked' : '' }}>
            <label for="enable_email" class="form-check-label">Enable Email Notifications</label>
          </div>

          <div class="form-check">
            <input type="checkbox" name="notifications[enable_sms]" id="enable_sms"
              class="form-check-input" value="1" {{ $settings['notifications']['enable_sms'] ? 'checked' : '' }}>
            <label for="enable_sms" class="form-check-label">Enable SMS Notifications</label>
          </div>

          <div class="form-check">
            <input type="checkbox" name="notifications[enable_dashboard]" id="enable_dashboard"
              class="form-check-input" value="1" {{ $settings['notifications']['enable_dashboard'] ? 'checked' : '' }}>
            <label for="enable_dashboard" class="form-check-label">Enable Dashboard Notifications</label>
          </div>
        </div>
      </div>

      <button type="submit" class="btn btn-primary mt-3">Save Settings</button>
    </form>
  </div>
</div>
@endsection

@push('styles')
<style>
  .select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #007bff;
    border-color: #006fe6;
    color: white;
  }
</style>
@endpush