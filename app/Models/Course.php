<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'language_id',
        'user_id',
        'title',
        'description',
        'short_description',
        'price',
        'discount_rate',
        'thumbnail_url',
        'level',
    ];

    /**
     * Get the topic that owns the course.
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Get the language that owns the course.
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * Get the user that owns the course.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
