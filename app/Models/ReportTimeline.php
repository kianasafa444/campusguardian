<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportTimeline extends Model
{
    protected $table = 'report_timeline';

    protected $fillable = [
        'report_id',
        'previous_status',
        'new_status',
        'note',
        'is_admin_note',
        'action_by',
    ];

    protected function casts(): array
    {
        return [
            'is_admin_note' => 'boolean',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function actionBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'action_by');
    }
}
