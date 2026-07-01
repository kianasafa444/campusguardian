<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    protected $fillable = [
        'report_id',
        'rating',
        'satisfaction_level',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }
}
