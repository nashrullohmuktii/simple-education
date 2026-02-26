<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'parent_id',
    ];

    /**
     * Get the parent topic.
     */
    public function parent()
    {
        return $this->belongsTo(Topic::class, 'parent_id');
    }

    /**
     * Get the child topics.
     */
    public function children()
    {
        return $this->hasMany(Topic::class, 'parent_id');
    }
}
