<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = ['English', 'Indonesian', 'Spanish', 'French', 'German'];

        foreach ($languages as $language) {
            Language::create(['name' => $language]);
        }
    }
}
