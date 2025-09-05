<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Notifications\DocumentExpiryReminder;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CheckDocumentExpiries extends Command
{
    protected $signature = 'documents:check-expiries';
    protected $description = 'Check for documents that are about to expire and send notifications';

    public function handle()
    {
        // Documents expiring in 7 days
        $soonExpiring = Document::where('expiry_date', '<=', Carbon::now()->addDays(7))
            ->where('expiry_date', '>', Carbon::now())
            ->where('is_expiry_notified', false)
            ->get();

        foreach ($soonExpiring as $document) {
            $document->uploader->notify(new DocumentExpiryReminder($document, 'soon'));
            $document->is_expiry_notified = true;
            $document->save();
        }

        // Documents that have expired today
        $expiredToday = Document::whereDate('expiry_date', Carbon::today())
            ->where('is_expiry_notified', true) // Already notified about upcoming expiry
            ->get();

        foreach ($expiredToday as $document) {
            $document->uploader->notify(new DocumentExpiryReminder($document, 'expired'));
        }

        $this->info('Expiry check completed. Notifications sent for ' . ($soonExpiring->count() + $expiredToday->count()) . ' documents.');
    }
}
