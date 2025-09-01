<?php

namespace App\Http\Controllers;

use App\Models\Award;
use App\Models\Find;
use App\Models\FindType;
use App\Models\Honor;
use App\Models\LatestNews;
use App\Models\Publication;
use App\Models\PublicationType;
use App\Models\Review;
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
                $header_values = array_map(function ($header) {
                    return trim($header);
                }, $r);
                continue;
            }
            $rows[] = array_combine($header_values, $r);
        }

        return $rows;
    }

    private function insertAwards($data)
    {
        $output = [];
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

            $output[] = $award;
        }

        return $output;
    }

    private function insertHonors($data)
    {
        $output = [];
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

            $output[] = $honor;
        }

        return $output;
    }

    private function insertNews($data)
    {
        $output = [];
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

            $output[] = $news;
        }

        return $output;
    }

    private function insertPublications($data)
    {
        $output = [];
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

            $output[] = $pub;
        }

        return $output;
    }

    private function insertReviewsQuotes($data)
    {
        $output = [];
        foreach ($data as $item) {
            $maxSortOrder = Review::max('sort_order');

            $rec = [
                'md_file' => '',
                'sort_order' => $maxSortOrder + 1,
            ];

            $reviewText = $item['Text of Review/Quote'] . "\n\n&mdash; " . $item['Attribution'];

            $review = Review::create($rec);
            $mdFile = "reviews/" . $review->id . ".md";
            Storage::disk('local')
                ->put($mdFile, $reviewText);

            $review->md_file = $mdFile;
            $review->save();

            $output[] = $review;
        }

        return $output;
    }

    private function insertSeeHearRead($data)
    {
        $output = [];
        foreach ($data as $item) {
            $type = FindType::where('name', strtolower($item['Type']))
                ->first();
            $rec = [
                'title' => $item['Text (highlighted as link to URL)'],
                'url' => $item['URL'],
                'date' => $item['Date'],
                'md_file' => '',
                'find_type_id' => $type->id
            ];

            $find = Find::create($rec);
            $mdFile = "finds/" . $find->id . ".md";
            Storage::disk('local')
                ->put($mdFile, $item['Note (follows highlighted Text)']);

            $find->md_file = $mdFile;
            $find->save();

            $output[] = $find;
        }

        return $output;
    }

    public function store(Request $request)
    {
        logger()->info('import-controller-store');

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
