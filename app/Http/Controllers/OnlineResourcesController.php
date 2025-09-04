<?php

namespace App\Http\Controllers;

use App\Models\BrokenLink;
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
            ->orderBy('name')
            ->with('brokenLink');

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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $valid = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|string'
        ]);

        $valid['online_resource_id'] = $request->online_resource['id'];
        $resource = OnlineResourceLink::create($valid);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $broken = BrokenLink::where('table_name', 'online_resource_links')
            ->where('table_id', $id)
            ->first();

        $broken->delete();

        $valid = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|string'
        ]);

        $valid['online_resource_id'] = $request->online_resource['id'];
        OnlineResourceLink::find($id)->update($valid);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        OnlineResourceLink::find($id)->delete();
        return response()->json(['status' => 'ok']);
    }
}
