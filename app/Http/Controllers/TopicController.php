<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Http\Requests\StoreTopicRequest;
use App\Http\Requests\UpdateTopicRequest;
use App\Http\Resources\TopicResource;
use App\Helpers\ResponseHelper;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $topics = Topic::all();
        return ResponseHelper::success(TopicResource::collection($topics), 'Topics retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTopicRequest $request)
    {
        $topic = Topic::create($request->validated());
        return ResponseHelper::success(new TopicResource($topic), 'Topic created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Topic $topic)
    {
        return ResponseHelper::success(new TopicResource($topic), 'Topic retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTopicRequest $request, Topic $topic)
    {
        $topic->update($request->validated());
        return ResponseHelper::success(new TopicResource($topic), 'Topic updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Topic $topic)
    {
        $topic->delete();
        return ResponseHelper::success([], 'Topic deleted successfully');
    }
}
