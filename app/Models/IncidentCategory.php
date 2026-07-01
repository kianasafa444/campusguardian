<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IncidentCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
