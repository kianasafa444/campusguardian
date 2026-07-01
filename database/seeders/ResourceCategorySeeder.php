<?php

namespace Database\Seeders;

use App\Models\ResourceCategory;
use Illuminate\Database\Seeder;

class ResourceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Anti Bullying', 'slug' => 'anti-bullying', 'description' => 'Informasi tentang bullying dan cara mengatasinya.', 'icon' => 'shield', 'sort_order' => 1],
            ['name' => 'Kontak Darurat', 'slug' => 'kontak-darurat', 'description' => 'Nomor telepon dan kontak penting dalam situasi darurat.', 'icon' => 'phone', 'sort_order' => 2],
            ['name' => 'Pertanyaan Umum (FAQ)', 'slug' => 'faq', 'description' => 'Pertanyaan yang sering diajukan tentang sistem pelaporan.', 'icon' => 'help-circle', 'sort_order' => 3],
            ['name' => 'Panduan', 'slug' => 'panduan', 'description' => 'Panduan dan tata cara penggunaan sistem pelaporan.', 'icon' => 'book', 'sort_order' => 4],
            ['name' => 'Kesehatan Mental', 'slug' => 'kesehatan-mental', 'description' => 'Informasi dan tips menjaga kesehatan mental.', 'icon' => 'heart', 'sort_order' => 5],
            ['name' => 'Hak & Perlindungan', 'slug' => 'hak-perlindungan', 'description' => 'Informasi tentang hak-hak mahasiswa dan perlindungan hukum.', 'icon' => 'scale', 'sort_order' => 6],
        ];

        foreach ($categories as $category) {
            ResourceCategory::create($category);
        }
    }
}
