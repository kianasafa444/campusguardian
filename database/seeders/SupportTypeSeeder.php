<?php

namespace Database\Seeders;

use App\Models\SupportType;
use Illuminate\Database\Seeder;

class SupportTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Konseling Psikologis', 'slug' => 'konseling-psikologis', 'description' => 'Dukungan kesehatan mental dan konseling dengan psikolog.'],
            ['name' => 'Pendampingan Akademik', 'slug' => 'pendampingan-akademik', 'description' => 'Bantuan terkait dampak insiden pada kegiatan akademik.'],
            ['name' => 'Bantuan Hukum', 'slug' => 'bantuan-hukum', 'description' => 'Pendampingan dan konsultasi hukum terkait insiden yang dilaporkan.'],
            ['name' => 'Pendampingan Satgas', 'slug' => 'pendampingan-satgas', 'description' => 'Pendampingan langsung oleh Satgas PPKS dalam proses penanganan.'],
        ];

        foreach ($types as $type) {
            SupportType::create($type);
        }
    }
}
