<?php

namespace App\Notifications;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public $document;

    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    public function via($notifiable)
    {
        return ['database'];
        //return ['mail', 'database'];

    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Document Approved: ' . $this->document->title)
            ->line('Your document has been approved.')
            ->line('Document: ' . $this->document->title)
            ->action('View Document', route('documents.show', $this->document->id))
            ->line('Thank you for using our system!');
    }

    public function toArray($notifiable)
    {
        return [
            'document_id' => $this->document->id,
            'title' => $this->document->title,
            'message' => 'Your document has been approved.',
            'url' => route('documents.show', $this->document->id),
        ];
    }
}
