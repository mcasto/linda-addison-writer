<?php

namespace App\Http\Controllers;

use App\Models\Find;
use App\Models\FindType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    public function adminIndex(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $searchTerm = $request->input('search', '');

        $query = Find::with('find_type')
            ->orderBy('date', 'desc');

        // Add search filter if search term exists
        if ($searchTerm) {
            $query->where('title', 'like', '%' . $searchTerm . '%');
        }

        $finds = $query->paginate($perPage);

        return response()->json($finds);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $id)
    {
        $defaultFindType = FindType::where('name', 'read')->first();

        $find = Find::create([
            'title' => $request->title ?? '',
            'url' => $request->url ?? '',
            'date' => $request->date ?? date("Y-m-d"),
            'find_type_id' => $request->find_type['id'] ?? $defaultFindType,
            'md_file' => ''
        ]);

        $mdFile = 'finds/' . $find->id . '.md';
        $find->md_file = $mdFile;
        $find->save();

        Storage::disk('local')->put($mdFile, $request->raw ?? '');

        return response()->json(['status' => 'ok']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $find = Find::find($id);
        $url = $find->url;
        $find->title = $request->title ?? '';
        $find->url = $request->url ?? '';
        $find->date = $request->date ?? '';
        $find->find_type_id = $request->find_type['id'];
        $find->save();

        if ($url != $request->url) {
            if (!is_null($find->brokenLink)) {
                $find->brokenLink->delete();
            }
        }

        $mdFile = $find->md_file;
        Storage::disk('local')->put($mdFile, $request->raw ?? '');

        return response()->json(['status' => 'ok']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $find = Find::find($id);
        $find->delete();

        return response()->json(['status' => 'ok']);
    }
}
