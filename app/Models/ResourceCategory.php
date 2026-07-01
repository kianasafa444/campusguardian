<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResourceCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }
}
