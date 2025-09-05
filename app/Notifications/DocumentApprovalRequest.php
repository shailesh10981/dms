<?php

namespace App\Notifications;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentApprovalRequest extends Notification implements ShouldQueue
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
            ->subject('Document Approval Request: ' . $this->document->title)
            ->line('You have a new document to approve.')
            ->line('Document: ' . $this->document->title)
            ->line('Department: ' . $this->document->department->name)
            ->action('Review Document', route('documents.show', $this->document->id))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'document_id' => $this->document->id,
            'title' => $this->document->title,
            'message' => 'Document requires your approval: ' . $this->document->title,
            'url' => route('documents.show', $this->document->id),
        ];
    }
}
