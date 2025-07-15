<?php

namespace App\Http\Controllers;

use App\Models\LatestNews;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json(LatestNews::orderBy('date', 'desc')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $id)
    {
        $rec = LatestNews::create([
            'date' => $request->date ?? date("Y-md"),
            'md_file' => ''
        ]);

        $rec->md_file = 'latest-news/' . $rec->id . '.md';

        $rec->save();

        Storage::disk('local')->put($rec->md_file, $request->raw ?? '');

        return response()->json(['status' => 'ok']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rec = LatestNews::find($id);
        $rec->date = $request->date ?? date("Y-m-d");
        $rec->save();

        Storage::disk('local')->put($rec->md_file, $request->raw ?? '');

        return response()->json(['status' => 'ok']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $rec = LatestNews::find($id);
        $rec->delete();

        return response()->json(['status' => 'ok']);
    }
}
