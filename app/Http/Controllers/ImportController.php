<?php

namespace App\Http\Controllers;

use App\Models\Award;
use App\Models\Biblio;
use App\Models\BiblioPub;
use App\Models\BiblioType;
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

            if (method_exists($this, $fnName)) {
                $output[$this->tableMap[$sheetName]] = $this->{$fnName}($data);
            }
        }

        return response()->json(['data' => $output]);
    }

    public function importBiblioText(Request $request)
    {
        if (!$request->hasFile('textFile')) {
            return response()->json(['status' => 'error', 'message' => 'No excel file uploaded.']);
        }

        $file = $request->textFile;
        $lines = file($file);

        $lineType = 0;
        $lineTypes = [
            'biblio',
            'publication'
        ];
        $output = [];
        $biblio = [];
        $biblioPub = [];
        foreach ($lines as  $line) {
            if (trim($line) === "") {
                continue;
            }

            if (preg_match("/(POETRY|FICTION|NONFICTION)/", trim($line), $m)) {
                $type = strtolower($m[1]);
                $typeId = BiblioType::where('type', trim($type))
                    ->first()
                    ->id;
                continue;
            }

            $recType = $lineTypes[$lineType];
            $lineType = $lineType == 0 ? 1 : 0;

            if ($recType == 'biblio') {
                $biblio['biblio_type_id'] = $typeId;
                $biblio['type'] = $type;
                $biblio['title'] = trim($line);
            }

            if ($recType == 'publication') {
                preg_match("/(.*)\s\((.*)\)/", $line, $m);
                $date = strtotime($m[2]);

                $biblioPub = [
                    'publication' => $m[1],
                    'pub_date' => date("Y-m-d", $date),
                    'display_date' => $m[2]
                ];

                $biblio['sort_date'] = $biblioPub['pub_date'];

                // at this point, based on the layout of the text file, the biblio & biblioPub recs should be ready for creation

                $output[] = [
                    'id' => uniqid(),
                    'biblio' => $biblio,
                    'biblioPub' => $biblioPub
                ];
                $biblio = [];
                $biblioPub = [];
            }
        }

        return response()->json($output);
    }
}
