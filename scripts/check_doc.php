<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\Document;
$ids = Document::orderBy('created_at','desc')->limit(10)->pluck('document_id');
echo json_encode($ids, JSON_PRETTY_PRINT), "\n";
