<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function supportRequests(): HasMany
    {
        return $this->hasMany(SupportRequest::class);
    }
}
