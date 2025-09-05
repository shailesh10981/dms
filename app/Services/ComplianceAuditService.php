<?php

namespace App\Services;

use App\Models\ComplianceReport; // ✅ Add this import

class ComplianceAuditService
{
  public static function log(ComplianceReport $report, string $action, ?string $comments = null, ?array $metadata = null)
  {
    return $report->auditLogs()->create([
      'user_id' => auth()->id(),
      'action' => $action,
      'comments' => $comments,
      'metadata' => $metadata ?? []
    ]);
  }
}
