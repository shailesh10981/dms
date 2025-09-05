<?php

namespace App\Notifications;

use App\Models\ComplianceReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportApprovedNotification extends Notification implements ShouldQueue
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
            ->subject('Your Compliance Report Has Been Approved')
            ->line('Your compliance report has been approved.')
            ->line('Report: ' . $this->report->title)
            ->action('View Report', route('compliance.reports.show', $this->report))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Report Approved',
            'message' => 'Your compliance report has been approved: ' . $this->report->title,
            'url' => route('compliance.reports.show', $this->report),
        ];
    }
}
