<?php

namespace App\Notifications;

use App\Models\ComplianceReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $report;

    public function __construct(ComplianceReport $report)
    {
        $this->report = $report;
    }

    public function via($notifiable)
    {
        return ['database'];
        //return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Compliance Report Submitted for Approval')
            ->line('A new compliance report has been submitted for your approval.')
            ->line('Report: ' . $this->report->title)
            ->action('Review Report', route('compliance.reports.show', $this->report))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'New Report Submitted',
            'message' => 'A new compliance report requires your approval: ' . $this->report->title,
            'url' => route('compliance.reports.show', $this->report),
        ];
    }
}
