<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Resources\CourseResource;
use App\Helpers\ResponseHelper;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::all();
        return ResponseHelper::success(CourseResource::collection($courses), 'Courses retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest $request)
    {
        $course = Course::create($request->validated());
        return ResponseHelper::success(new CourseResource($course), 'Course created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        return ResponseHelper::success(new CourseResource($course), 'Course retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseRequest $request, Course $course)
    {
        $course->update($request->validated());
        return ResponseHelper::success(new CourseResource($course), 'Course updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        $course->delete();
        return ResponseHelper::success([], 'Course deleted successfully');
    }
}
