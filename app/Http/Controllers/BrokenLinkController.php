<?php

namespace App\Http\Controllers;

use App\Models\BrokenLink;
use Illuminate\Http\Request;

class BrokenLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json($brokenLinks = BrokenLink::orderBy('updated_at', 'desc')
            ->orderBy('confirmed_working')
            ->with('linkable')
            ->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rec = BrokenLink::find($id);
        if (!$rec) {
            return response()->json(['status' => 'Item not found']);
        }

        $valid = $request->validate([
            'table_name' => 'required|string|max:255',
            'confirmed_broken' => 'required|boolean',
            'confirmed_working' => 'required|boolean',
            'confirmed_date' => 'nullable|date|sometimes'
        ]);

        $rec->update($valid);

        if (isset($request->updated_link)) {
            $field = $valid['table_name'] == 'covers' ? 'purchase_url' : 'url';
            $rec->linkable->update([$field => $request->updated_link]);
        }

        $response = ['status' => 'ok', 'deleteRec' => !!$rec->confirmed_working];

        // if ($response['deleteRec']) {
        //     $rec->delete();
        // }

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
