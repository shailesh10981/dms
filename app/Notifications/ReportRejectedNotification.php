<?php

namespace App\Notifications;

use App\Models\ComplianceReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $report;
    public $reason;

    public function __construct(ComplianceReport $report, $reason)
    {
        $this->report = $report;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['database'];
        //return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Compliance Report Requires Changes')
            ->line('Your compliance report has been rejected and requires changes.')
            ->line('Report: ' . $this->report->title)
            ->line('Reason: ' . $this->reason)
            ->action('View Report', route('compliance.reports.show', $this->report))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Report Rejected',
            'message' => 'Your compliance report was rejected: ' . $this->report->title,
            'url' => route('compliance.reports.show', $this->report),
        ];
    }
}
