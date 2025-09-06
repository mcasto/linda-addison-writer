<?php

namespace App\Http\Controllers;

use App\Models\Award;
use App\Models\Event;
use App\Models\Find;
use App\Models\FindType;
use App\Models\Honor;
use App\Models\LatestNews;
use App\Models\LifePoem;
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
    private $tableMap = [
        'seeHearRead' => 'See / Hear Read',
        'awards' => 'Awards',
        'events' => 'Events',
        'honors' => 'Honors',
        'latestNews' => 'Latest News',
        'publications' => 'Publications',
        'reviewsQuotes' => 'Reviews & Quotes',
        'lifePoems' => 'Life Poems'
    ];


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
                'year' => $item['year'],
                'md_file' => ''
            ];

            $award = Award::create($rec);
            $mdFile = "awards/" . $award->id . ".md";
            Storage::disk('local')
                ->put($mdFile, $item['text']);

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
                'year' => $item['year'],
                'md_file' => '',
                'num' => $item['num']
            ];

            $honor = Honor::create($rec);
            $mdFile = "honor/" . $honor->id . ".md";
            Storage::disk('local')
                ->put($mdFile, $item['text']);

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
                'date' => $item['date'],
                'md_file' => '',
            ];

            $news = LatestNews::create($rec);
            $mdFile = "latest-news/" . $news->id . ".md";
            Storage::disk('local')
                ->put($mdFile, $item['news']);

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
            $type = PublicationType::where('name', strtolower($item['type']))
                ->first();
            if (!$type) {
                $type = PublicationType::create(['name' => strtolower($item['type'])]);
            }
            $rec = [
                'publication_type_id' => $type->id,
                'year' => $item['year'],
                'title' => $item['title'],
                'md_file' => '',
                'url' => $item['url']
            ];

            $pub = Publication::create($rec);
            $mdFile = "publications/" . $pub->id . ".md";
            Storage::disk('local')
                ->put($mdFile, $item['text']);

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

            $reviewText = $item['text'] . "\n\n&mdash; " . $item['attribution'];

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
            $type = FindType::where('name', strtolower($item['type']))
                ->first();
            $rec = [
                'title' => $item['text'],
                'url' => $item['url'],
                'date' => $item['date'],
                'md_file' => '',
                'find_type_id' => $type->id
            ];

            $find = Find::create($rec);
            $mdFile = "finds/" . $find->id . ".md";
            Storage::disk('local')
                ->put($mdFile, $item['note']);

            $find->md_file = $mdFile;
            $find->save();

            $output[] = $find;
        }

        return $output;
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
                logger()->error('Undefined method: ' . $fnName);
                continue;
            }

            $data = $this->getSheetData($index);

            foreach ($data as $item) {
                if (method_exists($this, $fnName)) {
                    $output[$this->tableMap[$sheetName]] = $this->{$fnName}($data);
                }
            }
        }

        return response()->json(['data' => $output]);
    }
}
