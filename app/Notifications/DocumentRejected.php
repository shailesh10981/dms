<?php

namespace App\Notifications;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public $document;
    public $reason;

    public function __construct(Document $document, $reason = null)
    {
        $this->document = $document;
        $this->reason = $reason ?? 'No reason provided.';
    }

    public function via($notifiable)
    {
        return ['database'];
        //return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Document Rejected: ' . $this->document->title)
            ->line('Your document has been rejected.')
            ->line('Document: ' . $this->document->title)
            ->line('Reason: ' . $this->reason)
            ->action('View Document', route('documents.show', $this->document->id))
            ->line('Please review and re-submit if needed.');
    }

    public function toArray($notifiable)
    {
        return [
            'document_id' => $this->document->id,
            'title' => $this->document->title,
            'message' => 'Your document was rejected. Reason: ' . $this->reason,
            'url' => route('documents.show', $this->document->id),
        ];
    }
}
