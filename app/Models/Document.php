<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'document_id',
        'title',
        'description',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'department_id',
        'location_id',
        'project_id',
        'uploaded_by',
        'status',
        'current_approver_id',
        'rejection_reason',
        'version',
        'parent_id',
        'expiry_date',
        'is_expiry_notified',
        'visibility',
        'created_by',
        'modified_by',
        'created_date',
        'modified_date',
        'workflow_definition'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'workflow_definition' => 'array',
    ];

    // In app/Models/Document.php

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($document) {
            if (!$document->department_id) {
                $document->department_id = auth()->user()->department_id ?? null;
            }
            if (!isset($document->document_id) || empty($document->document_id)) {
                $document->document_id = $document->generateDocumentId();
            }
            if (empty($document->version)) {
                $document->version = 1;
            }
            $document->created_by = auth()->id();
            $document->created_date = now()->toDateString();
        });
        static::updating(function ($document) {
            $document->modified_by = auth()->id();
            $document->modified_date = now()->toDateString();
        });
    }



    public function generateDocumentId()
    {
        if ($this->parent_id) {
            $parent = $this->parent ?? Document::find($this->parent_id);

            if ($parent) {
                $parts = explode('-', $parent->document_id);
                $prefix = implode('-', array_slice($parts, 0, 3)); // DOC-FIN-HQ
                $date = now()->format('Ymd');

                $tries = 0;

                do {
                    $tries++;

                    $latestVersion = Document::where('parent_id', $parent->id)
                        ->orWhere('id', $parent->id)
                        ->orderBy('version', 'desc')
                        ->first();

                    $versionNumber = $latestVersion ? $latestVersion->version + 1 : 1;

                    $documentId = "{$prefix}-{$date}-" . str_pad($versionNumber, 4, '0', STR_PAD_LEFT);

                    $exists = Document::where('document_id', $documentId)->exists();

                    if (!$exists) {
                        return $documentId;
                    }

                    // safety break after 10 tries to avoid infinite loop
                    if ($tries > 10) {
                        throw new \Exception("Unable to generate unique document ID after 10 attempts.");
                    }
                } while (true);
            }
        }

        // New format: DEPTYYYY-MM-DD-NNN
        $dept = $this->department ? $this->department->code : 'DEPT';
        $date = now()->format('Y-m-d');
        $seq = Document::whereDate('created_at', today())->count() + 1;
        $id = $dept . $date . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
        $i = 0;
        while (Document::where('document_id', $id)->exists()) {
            $i++;
            $id = $dept . $date . '-' . str_pad($seq + $i, 3, '0', STR_PAD_LEFT);
        }
        return $id;
    }



    public function department()
    {
        return $this->belongsTo(Department::class)->withDefault([
            'code' => 'N/A' // Provide default values
        ]);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'current_approver_id');
    }

    public function parent()
    {
        return $this->belongsTo(Document::class, 'parent_id');
    }

    public function versions()
    {
        return $this->hasMany(Document::class, 'parent_id');
    }

    public function workflowSteps()
    {
        return $this->hasMany(DocumentWorkflowStep::class);
    }

    public function approvals()
    {
        return $this->hasMany(DocumentApproval::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(DocumentAuditLog::class);
    }

    public function isLatestVersion()
    {
        return $this->versions()->count() === 0;
    }

    public function getLatestVersionAttribute()
    {
        if ($this->isLatestVersion()) {
            return $this;
        }

        return $this->versions()->latest('version')->first();
    }

    public function getVersionChainAttribute()
    {
        $chain = collect();

        // Add parent documents
        $current = $this;
        while ($current->parent) {
            $chain->push($current->parent);
            $current = $current->parent;
        }

        // Add current document
        $chain->push($this);

        // Add child versions
        $chain = $chain->merge($this->versions);

        return $chain->sortBy('version');
    }



    public function logAction($action, $details = null)
    {
        return $this->auditLogs()->create([
            'user_id' => auth()->id(),
            'action' => $action,
            'details' => is_array($details) ? json_encode($details) : $details,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }


    public function getStatusBadgeAttribute()
    {
        switch ($this->status) {
            case 'approved':
                return '<span class="badge badge-success">Approved</span>';
            case 'rejected':
                return '<span class="badge badge-danger">Rejected</span>';
            case 'submitted':
                return '<span class="badge badge-info">Submitted</span>';
            case 'draft':
                return '<span class="badge badge-warning">Draft</span>';
            case 'resubmitted':
                return '<span class="badge badge-secondary">Resubmitted</span>';
            default:
                return '<span class="badge badge-light">' . ucfirst($this->status) . '</span>';
        }
    }
}
