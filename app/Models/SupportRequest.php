<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'report_id',
        'support_type_id',
        'description',
        'status',
        'assigned_to',
        'admin_notes',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function supportType(): BelongsTo
    {
        return $this->belongsTo(SupportType::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
