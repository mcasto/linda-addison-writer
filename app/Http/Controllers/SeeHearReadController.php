<?php

namespace App\Http\Controllers;

use App\Models\Find;
use App\Models\FindType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SeeHearReadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        // get all find types without finds
        $publicationTypes = FindType::orderBy('sort_order')->get();

        return response()->json($publicationTypes);
    }

    public function getFindsByType($typeId, Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $searchTerm = $request->input('search', '');

        $query = Find::where('find_type_id', $typeId)
            ->orderBy('date', 'desc');

        // Add search filter if search term exists
        if ($searchTerm) {
            $query->where('title', 'like', '%' . $searchTerm . '%');
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
