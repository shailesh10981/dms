<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    public function index()
    {
        $this->authorize('settings_manage');

        $settings = [
            'general' => [
                'app_name' => SystemSetting::getValue('app_name', config('app.name')),
                'timezone' => SystemSetting::getValue('timezone', config('app.timezone')),
                'date_format' => SystemSetting::getValue('date_format', 'Y-m-d'),
                'time_format' => SystemSetting::getValue('time_format', 'H:i:s'),
            ],
            'mail' => [
                'mail_from_name' => SystemSetting::getValue('mail_from_name', config('mail.from.name')),
                'mail_from_address' => SystemSetting::getValue('mail_from_address', config('mail.from.address')),
            ],
            'document' => [
                'document_retention_days' => SystemSetting::getValue('document_retention_days', 365),
                'max_file_size' => SystemSetting::getValue('max_file_size', 10), // in MB
                'allowed_file_types' => SystemSetting::getValue('allowed_file_types', ['pdf', 'doc', 'docx', 'xls', 'xlsx']),
            ],
            'compliance' => [
                'default_due_days' => SystemSetting::getValue('default_due_days', 7),
                'reminder_days_before' => SystemSetting::getValue('reminder_days_before', 3),
            ],
            'notifications' => [
                'enable_email' => SystemSetting::getValue('enable_email', true),
                'enable_sms' => SystemSetting::getValue('enable_sms', false),
                'enable_dashboard' => SystemSetting::getValue('enable_dashboard', true),
            ]
        ];

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $this->authorize('settings_manage');

        $validated = $request->validate([
            'general.app_name' => 'required|string|max:255',
            'general.timezone' => 'required|timezone',
            'general.date_format' => 'required|string',
            'general.time_format' => 'required|string',
            'mail.mail_from_name' => 'required|string|max:255',
            'mail.mail_from_address' => 'required|email',
            'document.document_retention_days' => 'required|integer|min:1',
            'document.max_file_size' => 'required|integer|min:1|max:50',
            'document.allowed_file_types' => 'required|array',
            'compliance.default_due_days' => 'required|integer|min:1',
            'compliance.reminder_days_before' => 'required|integer|min:1',
            'notifications.enable_email' => 'required|boolean',
            'notifications.enable_sms' => 'required|boolean',
            'notifications.enable_dashboard' => 'required|boolean',
        ]);

        foreach ($validated as $group => $settings) {
            foreach ($settings as $key => $value) {
                SystemSetting::setValue("{$group}.{$key}", $value);
            }
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully');
    }
}
