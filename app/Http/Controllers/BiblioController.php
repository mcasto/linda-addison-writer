<?php

namespace App\Http\Controllers;

use App\Models\Biblio;
use App\Models\BiblioType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BiblioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        // First get all publication types without publications
        $biblioTypes = BiblioType::orderBy('order')
            ->get();

        return response()->json($biblioTypes);
    }

    public function getBiblioByType($typeId, Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $searchTerm = $request->input('search', '');

        $query = Biblio::where('biblio_type_id', $typeId)
            ->with('biblio_pubs')
            ->orderBy('sort_date', 'desc');

        // Add search filter if search term exists
        if ($searchTerm) {
            $query->where('title', 'like', '%' . $searchTerm . '%');
        }

        $biblio = $query->paginate($perPage);

        return response()->json($biblio);
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
