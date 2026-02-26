<?php

namespace Database\Seeders;

use App\Models\Topic;
use Illuminate\Database\Seeder;

class TopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Parent Topics
        $tech = Topic::create([
            'name' => 'Technology',
            'description' => 'Software engineering, programming, and IT.'
        ]);
        $business = Topic::create([
            'name' => 'Business',
            'description' => 'Finance, marketing, and management.'
        ]);

        // Child Topics (Subtopics)
        Topic::create([
            'name' => 'Web Development',
            'description' => 'Building websites and web apps.',
            'parent_id' => $tech->id
        ]);
        Topic::create([
            'name' => 'Data Science',
            'description' => 'Data analysis, ML, and AI.',
            'parent_id' => $tech->id
        ]);
        Topic::create([
            'name' => 'Digital Marketing',
            'description' => 'SEO, social media, and ads.',
            'parent_id' => $business->id
        ]);
    }
}
