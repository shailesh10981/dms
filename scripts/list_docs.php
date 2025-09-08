<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\Document;
$rows = Document::orderBy('id')->get(['id','document_id']);
foreach ($rows as $r) echo $r->id."\t".$r->document_id."\n";