<?php

namespace App\Http\Controllers;

use App\Models\Freebie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        //
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
