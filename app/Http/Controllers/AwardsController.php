<?php

namespace App\Http\Controllers;

use App\Models\Award;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AwardsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json(Award::orderBy('year', 'desc')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // create award rec
        $rec = Award::create([
            'year' => $request->input('year'),
            'md_file' => ''
        ]);

        $mdFile = 'awards/' . $rec->id . '.md';

        $rec->md_file = $mdFile;
        $rec->save();

        // create contents file
        Storage::disk('local')->put($mdFile, $request->input('raw'));

        return response()->json(['status' => 'ok']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // update award rec
        $award = Award::find($id);
        $award->year = $request->input('year');
        $award->save();

        // update contents
        Storage::disk('local')->put($award->md_file, $request->input('raw'));

        return response()->json(['status' => 'ok']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $award = Award::find($id);

        // remove contents
        Storage::disk('local')->delete($award->md_file);

        // delete DB record
        $award->delete();

        return response()->json(['status' => 'ok']);
    }
}
