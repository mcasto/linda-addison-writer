<?php

namespace App\Http\Controllers;

use App\Models\LifePoem;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LifePoemsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $searchTerm = $request->input('search', '');

        $query = LifePoem::orderBy('date', 'desc');

        // Add search filter if search term exists
        if ($searchTerm) {
            // Get all poems first (we can't use where() on file contents)
            $allPoems = LifePoem::all();

            // Filter poems where the file content contains the search term
            $filteredIds = $allPoems->filter(function ($poem) use ($searchTerm) {
                if (Storage::disk('local')->exists($poem->md_file)) {
                    $content = Storage::disk('local')->get($poem->md_file);
                    return str_contains($content, $searchTerm);
                }
                return false;
            })->pluck('id');

            // Apply the filter to our query
            $query->whereIn('id', $filteredIds);
        }
        $poems = $query->paginate($perPage);

        return response()->json($poems);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // get most final date
        $finalDate = LifePoem::orderBy('date', 'desc')->first();

        $dateVal = Carbon::parse($finalDate->date)->addDay();

        $rec = LifePoem::create(
            [
                'date' => $dateVal,
                'md_file' => ''
            ]
        );

        $rec->md_file = 'life-poems/' . $rec->id . '.md';
        $rec->save();

        Storage::disk('local')->put($rec->md_file, $request->raw ?? '');

        return response()->json(['status' => 'ok']);
    }

    /**
     * Display today's life poem
     */
    public function show()
    {
        return response()->json(LifePoem::today());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return response()->json(['update' => $request->all()]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $rec = LifePoem::find($id);
        $rec->delete();

        return response()->json(['status' => 'ok']);
    }
}
