<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use App\Models\PublicationType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        // First get all publication types without publications
        $publicationTypes = PublicationType::orderBy('name')
            ->get();

        return response()->json($publicationTypes);
    }

    public function adminIndex(): JsonResponse
    {
        $pubTypes = PublicationType::orderBy('name')
            ->get();

        $pubs = Publication::with('publication_type')
            ->get()
            ->map(function ($pub) {
                return [
                    'id' => $pub->id,
                    'title' => $pub->title,
                    'year' => $pub->year,
                    'url' => $pub->url,
                    'publication_type' => $pub->publication_type,
                    'contents' => $pub->contents
                ];
            });

        return response()->json(['pubTypes' => $pubTypes, 'publications' => $pubs]);
    }

    public function getPublicationsByType($typeId, Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $searchTerm = $request->input('search', '');

        $query = Publication::where('publication_type_id', $typeId)
            ->orderBy('year', 'desc');

        // Add search filter if search term exists
        if ($searchTerm) {
            $query->where('title', 'like', '%' . $searchTerm . '%');
        }

        $publications = $query->paginate($perPage)
            ->through(function ($pub) {
                return [
                    'id' => $pub->id,
                    'title' => $pub->title,
                    'year' => $pub->year,
                    'url' => $pub->url,
                    'publication_type' => $pub->publication_type,
                    'contents' => $pub->contents
                ];
            });

        return response()->json($publications);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $valid = $request->validate([
            'year' => 'required|integer',
            'title' => 'required|string|max:255',
            'publication_type_id' => 'required|exists:publication_types,id',
            'url' => 'required|string|max:255',
            'md' => 'nullable|string',
        ]);

        $contents = $request->input('md');

        $rec = $valid;
        unset($rec['md']);
        $rec['md_file'] = "";
        $pub = Publication::create($rec);

        Storage::disk('local')->put("publications/" . $pub->id . ".md", $contents);

        $pub->md_file = "publications/" . $pub->id . ".md";
        $pub->save();

        return response()->json(['status' => 'ok']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $valid = $request->validate([
            'year' => 'required|integer',
            'title' => 'required|string|max:255',
            'publication_type_id' => 'required|exists:publication_types,id',
            'url' => 'required|string|max:255',
            'md' => 'nullable|string',
        ]);

        $contents = $request->input('md');
        Storage::disk('local')->put("publications/" . $id . ".md", $contents);

        $rec = $valid;
        unset($rec['md']);
        $rec['md_file'] = "publications/$id.md";

        $pub = Publication::findOrFail($id);
        $pub->update($rec);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Publication::findOrFail($id)
            ->delete();

        return response()->json(['status' => 'ok']);
    }
}
