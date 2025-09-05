<?php

namespace App\Notifications;

use App\Models\ComplianceReport;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ReportApprovalRequest extends Notification implements ShouldQueue
{
  use Queueable;

  public $report;

  public function __construct(ComplianceReport $report)
  {
    $this->report = $report;
  }

  public function via($notifiable)
  {
    return ['database', 'mail'];
  }

  public function toMail($notifiable)
  {
    return (new MailMessage)
      ->subject('Report Approval Request: ' . $this->report->template->name)
      ->line('A new compliance report requires your approval.')
      ->action('Review Report', route('compliance-reports.show', $this->report))
      ->line('Report ID: ' . $this->report->report_id);
  }

  public function toArray($notifiable)
  {
    return [
      'report_id' => $this->report->id,
      'template_name' => $this->report->template->name,
      'message' => 'New compliance report requires approval',
      'url' => route('compliance-reports.show', $this->report),
    ];
  }
}
