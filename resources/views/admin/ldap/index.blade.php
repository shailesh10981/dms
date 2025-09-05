@extends('layouts.app')

@section('title', 'LDAP Configuration')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">LDAP Configuration & Testing</h3>
                </div>
                <div class="card-body">
                    <!-- Connection Status -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon {{ $connectionStatus ? 'bg-success' : 'bg-danger' }}">
                                    <i class="fas {{ $connectionStatus ? 'fa-check' : 'fa-times' }}"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">LDAP Connection</span>
                                    <span class="info-box-number">{{ $connectionStatus ? 'Connected' : 'Disconnected' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-primary" id="testConnectionBtn">
                                <i class="fas fa-plug"></i> Test Connection
                            </button>
                        </div>
                    </div>

                    <!-- Configuration Display -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5>Current Configuration</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <td><strong>LDAP Host</strong></td>
                                        <td>{{ $config['connections']['default']['hosts'][0] ?? 'Not configured' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Port</strong></td>
                                        <td>{{ $config['connections']['default']['port'] ?? 'Not configured' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Base DN</strong></td>
                                        <td>{{ $config['connections']['default']['base_dn'] ?? 'Not configured' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Use SSL</strong></td>
                                        <td>{{ $config['connections']['default']['use_ssl'] ? 'Yes' : 'No' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Use TLS</strong></td>
                                        <td>{{ $config['connections']['default']['use_tls'] ? 'Yes' : 'No' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Test Authentication -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5>Test User Authentication</h5>
                            <form id="testAuthForm">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="test_username">Username</label>
                                            <input type="text" class="form-control" id="test_username" name="username" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="test_password">Password</label>
                                            <input type="password" class="form-control" id="test_password" name="password" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <div>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-user-check"></i> Test Authentication
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Get User Info -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5>Get User Information</h5>
                            <form id="getUserInfoForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="info_username">Username</label>
                                            <input type="text" class="form-control" id="info_username" name="username" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <div>
                                                <button type="submit" class="btn btn-info">
                                                    <i class="fas fa-info-circle"></i> Get User Info
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Results Area -->
                    <div class="row">
                        <div class="col-12">
                            <div id="results" style="display: none;">
                                <h5>Results</h5>
                                <div class="alert" id="resultAlert"></div>
                                <pre id="resultData" style="background: #f8f9fa; padding: 15px; border-radius: 5px;"></pre>
                            </div>
                        </div>
                    </div>

                    <!-- Role Mapping Configuration -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Role Mapping Configuration</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Application Role</th>
                                            <th>LDAP Groups</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($config['role_mapping']['groups'] as $role => $groups)
                                        <tr>
                                            <td><strong>{{ $role }}</strong></td>
                                            <td>
                                                @foreach($groups as $group)
                                                    <span class="badge badge-secondary">{{ $group }}</span><br>
                                                @endforeach
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Test Connection
    $('#testConnectionBtn').click(function() {
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Testing...');
        
        $.get('{{ route("admin.ldap.test-connection") }}')
            .done(function(response) {
                showResult(response.success ? 'success' : 'danger', response.message);
            })
            .fail(function() {
                showResult('danger', 'Request failed');
            })
            .always(function() {
                btn.prop('disabled', false).html('<i class="fas fa-plug"></i> Test Connection');
            });
    });

    // Test Authentication
    $('#testAuthForm').submit(function(e) {
        e.preventDefault();
        const form = $(this);
        const btn = form.find('button[type="submit"]');
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Testing...');
        
        $.post('{{ route("admin.ldap.test-auth") }}', form.serialize())
            .done(function(response) {
                showResult(response.success ? 'success' : 'danger', response.message, response.user);
            })
            .fail(function() {
                showResult('danger', 'Request failed');
            })
            .always(function() {
                btn.prop('disabled', false).html('<i class="fas fa-user-check"></i> Test Authentication');
            });
    });

    // Get User Info
    $('#getUserInfoForm').submit(function(e) {
        e.preventDefault();
        const form = $(this);
        const btn = form.find('button[type="submit"]');
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Getting Info...');
        
        $.post('{{ route("admin.ldap.user-info") }}', form.serialize())
            .done(function(response) {
                showResult(response.success ? 'success' : 'danger', response.message, response.user);
            })
            .fail(function() {
                showResult('danger', 'Request failed');
            })
            .always(function() {
                btn.prop('disabled', false).html('<i class="fas fa-info-circle"></i> Get User Info');
            });
    });

    function showResult(type, message, data = null) {
        const alert = $('#resultAlert');
        const dataDiv = $('#resultData');
        
        alert.removeClass('alert-success alert-danger alert-info')
             .addClass('alert-' + type)
             .text(message);
        
        if (data) {
            dataDiv.text(JSON.stringify(data, null, 2)).show();
        } else {
            dataDiv.hide();
        }
        
        $('#results').show();
    }
});
</script>
@endpush
@endsection

