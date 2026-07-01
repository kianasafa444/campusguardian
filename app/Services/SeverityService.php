<?php

namespace App\Services;

class SeverityService
{
    public function determineByCategory(int $categoryId): string
    {
        return match ($categoryId) {
            1 => 'High',
            2 => 'Medium',
            3 => 'Medium',
            4 => 'Emergency',
            5 => 'High',
            6 => 'Emergency',
            7 => 'Low',
            default => 'Medium',
        };
    }
}
