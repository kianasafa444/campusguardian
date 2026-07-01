<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentVerification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'email',
        'email_domain',
        'contact_email',
        'verification_token',
        'otp_hash',
        'otp_attempts',
        'max_attempts',
        'is_verified',
        'verified_at',
        'otp_expires_at',
        'ip_address_hash',
        'user_agent_hash',
    ];

    protected $hidden = [
        'otp_hash',
        'ip_address_hash',
        'user_agent_hash',
    ];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'otp_attempts' => 'integer',
            'max_attempts' => 'integer',
        ];
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'student_verification_id');
    }
}
