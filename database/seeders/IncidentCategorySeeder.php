<?php

namespace Database\Seeders;

use App\Models\IncidentCategory;
use Illuminate\Database\Seeder;

class IncidentCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Pelecehan Seksual', 'slug' => 'pelecehan-seksual', 'description' => 'Perilaku seksual yang tidak diinginkan, termasuk komentar, sentuhan, atau tindakan seksual lainnya.', 'sort_order' => 1],
            ['name' => 'Bullying', 'slug' => 'bullying', 'description' => 'Perundungan fisik atau verbal yang dilakukan secara berulang.', 'sort_order' => 2],
            ['name' => 'Cyberbullying', 'slug' => 'cyberbullying', 'description' => 'Perundungan yang dilakukan melalui media digital atau sosial media.', 'sort_order' => 3],
            ['name' => 'Kekerasan Fisik', 'slug' => 'kekerasan-fisik', 'description' => 'Tindakan kekerasan yang melibatkan kontak fisik dan menyebabkan cedera.', 'sort_order' => 4],
            ['name' => 'Stalking', 'slug' => 'stalking', 'description' => 'Perilaku menguntit atau mengawasi seseorang secara terus-menerus.', 'sort_order' => 5],
            ['name' => 'Ancaman', 'slug' => 'ancaman', 'description' => 'Ancaman kekerasan atau bahaya terhadap seseorang atau kelompok.', 'sort_order' => 6],
            ['name' => 'Diskriminasi', 'slug' => 'diskriminasi', 'description' => 'Perlakuan tidak adil berdasarkan SARA, gender, atau identitas lainnya.', 'sort_order' => 7],
        ];

        foreach ($categories as $category) {
            IncidentCategory::create($category);
        }
    }
}
