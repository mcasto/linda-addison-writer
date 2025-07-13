<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventsController extends Controller
{
    public function getPast(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);

        return response()->json(
            Event::where('end_date', '<=', Carbon::now())
                ->orderBy('start_date', 'desc')
                ->paginate($perPage)
        );
    }

    public function getFuture(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);

        return response()->json(
            Event::where('start_date', '>=', Carbon::now())
                ->orderBy('start_date', 'desc')
                ->paginate($perPage)
        );
    }

    public function getCurrent(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);

        return response()->json(
            Event::where('start_date', '<=', Carbon::now())
                ->orderBy('start_date', 'desc')
                ->where('end_date', '>=', Carbon::now())
                ->orderBy('start_date', 'desc')
                ->paginate($perPage)
        );
    }

    /**
     * Get all events
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        return Event::orderby('start_date', 'desc')->paginate($perPage);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $id)
    {
        $rec = $request->only(['name', 'schedule', 'start_date', 'start_time', 'end_date', 'end_time', 'tz']);

        $rec['url'] = $request->url ?? '';
        $contents = $request->raw ?? '';

        $rec['md_file'] = '';

        $event = Event::create($rec);

        $mdFile = 'events/' . $event->id . '.md';

        $event->md_file = $mdFile;
        $event->save();

        Storage::disk('local')->put($mdFile, $contents);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rec = Event::find($id);
        $rec->name = $request->name ?? '';
        $rec->schedule = $request->schedule ?? '';
        $rec->start_date = $request->start_date;
        $rec->start_time = $request->start_time;
        $rec->end_date = $request->end_date;
        $rec->end_time = $request->end_time;
        $rec->url = $request->url ?? '';
        $rec->tz = $request->tz;
        $rec->md_file = 'events/' . $rec->id . '.md';
        $rec->save();

        Storage::disk('local')->put($rec->md_file, $request->raw ?? '');

        return response()->json(['status' => 'ok']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $event = Event::find($id);
        $event->delete();

        return response()->json(['status' => 'ok']);
    }
}
