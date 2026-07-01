<?php

namespace App\Services;

use App\Models\Report;
use Illuminate\Support\Str;

class TrackingIdService
{
    public function generate(): string
    {
        do {
            $id = 'CG-' . strtoupper(Str::random(8));
        } while (Report::where('tracking_id', $id)->exists());

        return $id;
    }
}
