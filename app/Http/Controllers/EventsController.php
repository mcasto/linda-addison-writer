<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
