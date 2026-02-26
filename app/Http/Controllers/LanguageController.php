<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Http\Requests\StoreLanguageRequest;
use App\Http\Requests\UpdateLanguageRequest;
use App\Http\Resources\LanguageResource;
use App\Helpers\ResponseHelper;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $languages = Language::all();
        return ResponseHelper::success(LanguageResource::collection($languages), 'Languages retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLanguageRequest $request)
    {
        $language = Language::create($request->validated());
        return ResponseHelper::success(new LanguageResource($language), 'Language created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Language $language)
    {
        return ResponseHelper::success(new LanguageResource($language), 'Language retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLanguageRequest $request, Language $language)
    {
        $language->update($request->validated());
        return ResponseHelper::success(new LanguageResource($language), 'Language updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Language $language)
    {
        $language->delete();
        return ResponseHelper::success([], 'Language deleted successfully');
    }
}
