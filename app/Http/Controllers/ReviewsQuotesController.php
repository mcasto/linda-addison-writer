<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReviewsQuotesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json(Review::orderBy('sort_order')
            ->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $sortOrder = Review::max('sort_order');
        $rec = [
            'sort_order' => $sortOrder + 1,
            'md_file' => ''
        ];

        $review = Review::create($rec);
        $mdFile = 'reviews/' . $review->id . '.md';
        $review->md_file = $mdFile;
        $review->save();

        Storage::disk('local')
            ->put($mdFile, $request->review);

        return response()->json(['status' => 'ok', 'review' => $review]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $recs = $request->all();

        // get ids from submited recs
        $idList = array_column($recs, 'id');

        // delete items not in updated list
        Review::whereNotIn('id', $idList)
            ->delete();

        foreach ($recs as $rec) {
            Review::find($rec['id'])
                ->update([
                    'sort_order' => $rec['sort_order'],
                ]);
        }

        return response()->json(['status' => 'ok']);
    }
}
