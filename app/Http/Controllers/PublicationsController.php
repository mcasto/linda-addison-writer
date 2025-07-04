<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use App\Models\PublicationType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        // First get all publication types without publications
        $publicationTypes = PublicationType::all();

        return response()->json($publicationTypes);
    }

    public function getPublicationsByType($typeId, Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $searchTerm = $request->input('search', '');

        $query = Publication::where('publication_type_id', $typeId)
            ->orderBy('year', 'desc');

        // Add search filter if search term exists
        if ($searchTerm) {
            $query->where('title', 'like', '%' . $searchTerm . '%');
        }

        $publications = $query->paginate($perPage);

        return response()->json($publications);
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
