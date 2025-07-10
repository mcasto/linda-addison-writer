<?php

namespace App\Http\Controllers;

use App\Models\Biblio;
use App\Models\BiblioPub;
use App\Models\BiblioType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BiblioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $biblioTypes = BiblioType::orderBy('order')
            ->get();

        return response()->json($biblioTypes);
    }

    public function getBiblioByType($typeId, Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $searchTerm = $request->input('search', '');

        $query = Biblio::where('biblio_type_id', $typeId)
            ->with('biblio_pubs')
            ->orderBy('sort_date', 'desc');

        // Add search filter if search term exists
        if ($searchTerm) {
            $query->where('title', 'like', '%' . $searchTerm . '%');
        }

        $biblio = $query->paginate($perPage);

        return response()->json($biblio);
    }


    /**
     * Save a biblio entry
     */
    public function save(Request $request)
    {
        $biblio = $request->all();

        // check if id is new or not
        $isNew = substr($request->id, 0, 4) == 'new-';

        if ($isNew) {
            unset($biblio['id']);
            unset($biblio['biblio_pubs']);

            $rec = Biblio::create($biblio);
        } else {
            unset($biblio['biblio_pubs']);

            $rec = Biblio::find($biblio['id']);

            $rec->update($biblio);
            $rec->save();
        }

        // handle pubs
        foreach ($request->biblio_pubs as $pub) {
            // check for delete
            $deleted = $pub['deleted'] ?? false;

            if ($deleted) {
                $pubRec = BiblioPub::find($pub['id']);
                $pubRec->delete();
                continue;
            }

            // is new
            $isNew = substr($pub['id'], 0, 4) == 'new-';

            if ($isNew) {
                unset($pub['id']);
                $pub['biblio_id'] = $rec->id;
                $pubRec = BiblioPub::create($pub);
            } else {
                $pubRec = BiblioPub::find($pub['id']);
                $pubRec->update($pub);
                $pubRec->save();
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $biblio = Biblio::find($id);
        $biblio->delete();

        return response()->json(['status' => 'ok']);
    }
}
