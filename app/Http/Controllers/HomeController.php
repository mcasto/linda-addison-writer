<?php

namespace App\Http\Controllers;

use App\Models\Cover;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * Move temp cover file to public disk
     */
    private function moveTempCover($fileId)
    {
        // 1. Search for the file in 'temp/' with any extension
        $tempDisk = Storage::disk('local');
        $matchingFiles = collect($tempDisk->files('temp'))->filter(function ($file) use ($fileId) {
            return pathinfo($file, PATHINFO_FILENAME) == $fileId;
        });

        // 2. Check if a matching file exists
        if ($matchingFiles->isEmpty()) {
            return false;
        }

        // 3. Get the first matching file (e.g., 'temp/7.jpeg')
        $sourcePath = $matchingFiles->first();
        $extension = pathinfo($sourcePath, PATHINFO_EXTENSION); // Extract extension

        // 4. Define the destination path (e.g., 'covers/7.jpeg')
        $destinationPath = "covers/{$fileId}.{$extension}";

        // 5. Move the file from 'local' to 'public' disk
        $fileContent = $tempDisk->get($sourcePath);
        Storage::disk('public')->put($destinationPath, $fileContent);
        $tempDisk->delete($sourcePath); // Delete the original

        return $destinationPath; // Return new path (optional)
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json(Cover::orderBy('sort_order')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $id)
    {
        $rec = $request->only(['title', 'purchase_url', 'sort_order']);
        $rec = [
            'title' => $request->title ?? 'New Cover',
            'purchase_url' => $request->purchase_url ?? '',
            'sort_order' => $request->sort_order ?? 0
        ];
        $rec['image'] = '';
        $rec['md_file'] = '';

        $cover = Cover::create($rec);

        $mdFile = 'covers/' . $cover->id . '.md';

        // write contents to mdFile
        Storage::disk('local')->put($mdFile, $request->raw ?? '');

        $cover->md_file = $mdFile;
        $cover->save();

        $replaceImage = $request->replaceImage;

        // replace image
        if ($replaceImage) {
            // move temp image
            $newImagePath = $this->moveTempCover($id);
            if (!$newImagePath) {
                return response()->json(['status' => 'error', 'message' => 'Unable to replace image']);
            }

            if ($newImagePath != $cover->image) {
                // update image in db
                $cover->image = $newImagePath;
                $cover->save();
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $replaceImage = $request->replaceImage;

        // update db rec
        $cover = Cover::find($id);
        $cover->title = $request->title;
        $cover->purchase_url = $request->purchase_url;
        $cover->sort_order = $request->sort_order;
        $cover->save();

        // update md file
        $mdFile = $cover->md_file;
        Storage::disk('local')->put($mdFile, $request->raw);

        // replace image
        if ($replaceImage) {
            // move temp image
            $newImagePath = $this->moveTempCover($id);
            if (!$newImagePath) {
                return response()->json(['status' => 'error', 'message' => 'Unable to replace image']);
            }

            if ($newImagePath != $cover->image) {
                // delete current image
                Storage::disk('public')->delete($cover->image);

                // update image in db
                $cover->image = $newImagePath;
                $cover->save();
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function uploadImage(Request $request, string $id)
    {
        $cover = Cover::find($id);

        if (!$cover && substr($id, 0, 4) != 'new-') {
            return response()->json(['error' => 'Invalid ID']);
        }

        if (!$request->hasFile('imageFile')) {
            return response()->json(['error' => 'No image file uploaded']);
        }

        $file = $request->imageFile;

        $file->storeAs('temp', $id . "." . $file->extension(), 'local');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cover = Cover::findOrFail($id);
        $cover->delete();

        return response()->json(['status' => 'ok']);
    }
}
