<?php

namespace App\Http\Controllers;

use App\Models\BrokenLink;
use App\Models\Social;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SocialsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json(Social::orderBy('sort_order')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // mc-todo: add validation

        // get sortOrder
        $sortOrder = Social::max('sort_order');
        $sortOrder++;

        // create social rec
        $social = Social::create([
            'icon' => $request->icon,
            'url' => $request->url,
            'sort_order' => $sortOrder
        ]);

        // return social rec

        return response()->json(['social' => $social]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $valid = $request->validate([
            'icon' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'sort_order' => 'required|integer'
        ]);

        // when updating, check if url differs from request->url. if it does, check for related brokenLink & delete it if it exists
        $social = Social::findOrFail($id);
        if ($social->url != $request->url) {
            $broken = BrokenLink::where('table_name', 'socials')
                ->where('table_id', $social->id)
                ->first();

            if ($broken) {
                $broken->delete();
            }
        }

        $social->update($valid);

        return response()->json(['existing' => $social]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $social = Social::find($id)->delete();
        return response()->json(['destroy' => $id, 'item' => $social]);
    }
}
