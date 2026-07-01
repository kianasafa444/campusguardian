<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tracking_id',
        'incident_category_id',
        'severity',
        'description',
        'location',
        'incident_date',
        'voice_to_text_raw',
        'voice_file_path',
        'status',
        'assigned_to',
        'student_verification_id',
        'ip_address_hash',
        'user_agent_hash',
        'submitted_at',
        'resolved_at',
    ];

    protected $hidden = [
        'student_verification_id',
        'ip_address_hash',
        'user_agent_hash',
    ];

    protected function casts(): array
    {
        return [
            'incident_date' => 'datetime',
            'submitted_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(IncidentCategory::class, 'incident_category_id');
    }

    public function studentVerification(): BelongsTo
    {
        return $this->belongsTo(StudentVerification::class, 'student_verification_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function evidences(): HasMany
    {
        return $this->hasMany(ReportEvidence::class);
    }

    public function timeline(): HasMany
    {
        return $this->hasMany(ReportTimeline::class)->latest();
    }

    public function visibleTimeline(): HasMany
    {
        return $this->hasMany(ReportTimeline::class)
            ->where('is_admin_note', false)
            ->latest();
    }

    public function supportRequests(): HasMany
    {
        return $this->hasMany(SupportRequest::class);
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(Feedback::class);
    }

    public function scopeActive(Builder $query): void
    {
        $query->whereNotIn('status', ['Resolved', 'Closed', 'Rejected']);
    }

    public function scopeEmergency(Builder $query): void
    {
        $query->where('severity', 'Emergency')
            ->whereNotIn('status', ['Resolved', 'Closed', 'Rejected']);
    }

    public function scopeByStatus(Builder $query, string $status): void
    {
        $query->where('status', $status);
    }

    public function scopeBySeverity(Builder $query, string $severity): void
    {
        $query->where('severity', $severity);
    }
}
