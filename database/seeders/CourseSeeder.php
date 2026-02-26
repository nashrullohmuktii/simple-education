<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Language;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure there are related models to attach to Courses
        if (User::count() === 0 || Topic::count() === 0 || Language::count() === 0) {
            $this->command->info('Please run User, Topic, and Language seeders first.');
            return;
        }

        $topics = Topic::all();
        $languages = Language::all();
        // Assuming admin can also be the author/instructor for the scope of this seeder
        $instructor = User::role('admin')->first() ?? User::first();

        $courses = [
            [
                'title' => 'Laravel 12 Masterclass',
                'description' => 'A comprehensive guide to building modern apps with Laravel 12.',
                'short_description' => 'Master the Laravel framework.',
                'price' => 199.90,
                'discount_rate' => 10.00,
                'thumbnail_url' => 'https://example.com/images/laravel.jpg',
                'level' => 'intermediate',
            ],
            [
                'title' => 'Vue 3 for Beginners',
                'description' => 'Learn Vue 3 from scratch with the Composition API.',
                'short_description' => 'Start building reactive UIs.',
                'price' => 50.00,
                'discount_rate' => 0.00,
                'thumbnail_url' => 'https://example.com/images/vue.jpg',
                'level' => 'beginner',
            ],
            [
                'title' => 'Advanced Data Structures in Python',
                'description' => 'Dive deep into algorithms and data structures.',
                'short_description' => 'Prepare for coding interviews.',
                'price' => 149.00,
                'discount_rate' => 25.50,
                'thumbnail_url' => 'https://example.com/images/ds.jpg',
                'level' => 'advance',
            ]
        ];

        foreach ($courses as $index => $courseData) {
            $courseData['topic_id'] = $topics->random()->id;
            $courseData['language_id'] = $languages->random()->id;
            $courseData['user_id'] = $instructor->id;

            Course::create($courseData);
        }
    }
}
