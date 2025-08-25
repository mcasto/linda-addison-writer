<?php

namespace App\Http\Controllers;

use App\Models\OnlineResource;
use App\Models\OnlineResourceLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OnlineResourcesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $resourceTypes = OnlineResource::orderBy('sort_order')
            ->get();

        return response()->json($resourceTypes);
    }

    public function getResourceLinksByType($typeId, Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $searchTerm = $request->input('search', '');

        $query = OnlineResourceLink::where('online_resource_id', $typeId)
            ->orderBy('name');

        // Add search filter if search term exists
        if ($searchTerm) {
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }

        $publications = $query->paginate($perPage);

        return response()->json($publications);
    }

    public function adminIndex(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $searchTerm = $request->input('search', '');

        $query = OnlineResourceLink::with('online_resource');

        // Add search filter if search term exists
        if ($searchTerm) {
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }

        $finds = $query->paginate($perPage);

        return response()->json($finds);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
