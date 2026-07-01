<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportEvidence extends Model
{
    protected $table = 'report_evidences';

    protected $fillable = [
        'report_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'mime_type',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }
}
