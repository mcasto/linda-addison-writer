<?php

namespace App\Http\Controllers;

use App\Models\Honor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HonorsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json(Honor::orderBy('year', 'desc')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $id)
    {
        $rec = Honor::create([
            'year' => $request->year ?? date("Y"),
            'num' => $request->num ?? 0,
            'md_file' => ''
        ]);

        $rec->md_file = 'honors/' . $rec->id . '.md';

        $rec->save();

        // update md file
        Storage::disk('local')->put($rec->md_file, $request->raw ?? 'No honors detailed.');

        return response()->json(['status' => 'ok']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rec = Honor::find($id);
        $rec->num = $request->num ?? 0;
        $rec->year = $request->year ?? date("Y");

        $rec->save();


        Storage::disk('local')->put($rec->md_file, $request->raw ?? 'No honors detailed.');

        return response()->json(['status' => 'ok']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $rec = Honor::find($id);
        $rec->delete();
        return response()->json(['status' => 'ok']);
    }
}
