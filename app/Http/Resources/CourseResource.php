<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'topic_id' => $this->topic_id,
            'language_id' => $this->language_id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'price' => (float) $this->price,
            'discount_rate' => (float) $this->discount_rate,
            'thumbnail_url' => $this->thumbnail_url,
            'level' => $this->level,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
