<?php

namespace App\Http\Controllers;

use App\Models\Award;
use App\Models\Honor;
use App\Models\LatestNews;
use App\Models\Publication;
use App\Models\PublicationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Shuchkin\SimpleXLSX;
use Illuminate\Support\Str;

class ImportController extends Controller
{
    private $sheetList;
    private $xl;


    private function getSheetData($index)
    {
        // Produce array keys from the array values of 1st array element
        $header_values = $rows = [];
        foreach ($this->xl->rows($index) as $k => $r) {
            if ($k === 0) {
                $header_values = $r;
                continue;
            }
            $rows[] = array_combine($header_values, $r);
        }

        return $rows;
    }

    private function insertAwards($data)
    {
        foreach ($data as $item) {
            $rec = [
                'year' => $item['Year'],
                'md_file' => ''
            ];

            $award = Award::create($rec);
            $mdFile = "awards/" . $award->id . ".md";
            Storage::disk('local')
                ->put($mdFile, $item['Award Description']);

            $award->md_file = $mdFile;
            $award->save();
        }

        return $data;
    }

    private function insertHonors($data)
    {
        foreach ($data as $item) {
            $rec = [
                'year' => $item['Year'],
                'md_file' => '',
                'num' => $item['Number of Honors Received']
            ];

            $honor = Honor::create($rec);
            $mdFile = "honor/" . $honor->id . ".md";
            Storage::disk('local')
                ->put($mdFile, $item['Description of Honor']);

            $honor->md_file = $mdFile;
            $honor->save();
        }

        return $data;
    }

    private function insertNews($data)
    {
        foreach ($data as $item) {
            $rec = [
                'date' => $item['Date'],
                'md_file' => '',
            ];

            $news = LatestNews::create($rec);
            $mdFile = "latest-news/" . $news->id . ".md";
            Storage::disk('local')
                ->put($mdFile, $item['News']);

            $news->md_file = $mdFile;
            $news->save();
        }

        return $data;
    }

    private function insertPublications($data)
    {
        foreach ($data as $item) {
            $type = PublicationType::where('name', strtolower($item['Type']))
                ->first();
            if (!$type) {
                $type = PublicationType::create(['name' => strtolower($item['Type'])]);
            }
            $rec = [
                'publication_type_id' => $type->id,
                'year' => $item['Year'],
                'title' => $item['Title'],
                'md_file' => '',
                'url' => $item['URL']
            ];

            $pub = Publication::create($rec);
            $mdFile = "publications/" . $pub->id . ".md";
            Storage::disk('local')
                ->put($mdFile, $item['Description']);

            $pub->md_file = $mdFile;
            $pub->save();
        }

        return $data;
    }

    private function insertReviewsQuotes($data)
    {
        return $data;
    }

    private function insertSeeHearRead($data)
    {
        return $data;
    }

    public function store(Request $request)
    {
        if (!$request->hasFile('excelFile')) {
            return response()->json(['status' => 'error', 'message' => 'No excel file uploaded.']);
        }

        $file = $request->excelFile;

        $this->xl = SimpleXLSX::parse($file);

        if (!$this->xl) {
            return response()->json(['status' => 'error', 'message' => 'Invalid or corrupt XLSX File']);
        }

        $this->sheetList = $this->xl->sheetNames();

        $output = [];
        foreach ($this->sheetList as $index => $sheetName) {
            if ($this->xl->isHiddenSheet($index)) {
                continue;
            }

            $fnName = 'insert' . Str::studly(str_replace('&', '', $sheetName));
            if (!method_exists($this, $fnName)) {
                return response()->json(['status' => 'error', 'message' => 'Undefined method: ' . $fnName]);
            }

            $data = $this->getSheetData($index);


            $output[$sheetName] = count($data) > 0 ? $this->{$fnName}($data) : [];
        }

        return response()->json(['data' => $output]);
    }
}
