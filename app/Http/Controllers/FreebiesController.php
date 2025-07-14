<?php

namespace App\Http\Controllers;

use App\Models\Freebie;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FreebiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Freebie::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $final = Freebie::orderBy('end_date', 'desc')
            ->first();

        $startDate = new Carbon($final->end_date . " + 1 day")->toDateString();
        $endDate = new Carbon($startDate . " + 6 days")->toDateString();

        $freebie = [
            'title' => $request->title ?? 'New Freebie',
            'note' => $request->note ?? '',
            'sub' => $request->sub ?? '',
            'md_file' => '',
            'start_date' => $startDate,
            'end_date' => $endDate
        ];

        $rec = Freebie::create($freebie);

        $rec->md_file = 'freebies/' . $rec->id . '.md';

        // update markdown contents
        Storage::disk('local')->put($rec->md_file, $request->raw ?? '');

        // update db
        $rec->save();

        return response()->json(['status' => 'ok']);
    }

    /**
     * retrieve today's freebie
     */
    public function show(): JsonResponse
    {
        return response()->json(Freebie::today());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $rec = Freebie::find($id);
        $rec->title = $request->title ?? '';
        $rec->note = $request->note ?? '';
        $rec->sub = $request->sub ?? '';
        $rec->md_file = 'freebies/' . $rec->id . '.md';

        // update markdown contents
        Storage::disk('local')->put($rec->md_file, $request->raw ?? '');

        // update db
        $rec->save();

        return response()->json(['status' => 'ok']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $rec = Freebie::find($id);
        $rec->delete();

        return response()->json(['status' => 'ok']);
    }
}
