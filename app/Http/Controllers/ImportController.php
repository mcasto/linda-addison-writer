<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Shuchkin\SimpleXLSX;

class ImportController extends Controller
{
    private $sheetList;
    private $xl;
    private $maps = [
        'Awards' => [
            'table' => 'awards',
            'special' => 'false',
            'columns' => [
                'Year' => 'year',
                'Award Description' => 'md_file'
            ]
        ],
        'Honors' => [
            'table' => 'honors',
            'special' => 'false',
            'columns' => [
                'Year' => 'year',
                'Description of Honor' => 'md_file',
                'Number of Honors Received' => 'num'
            ]
        ],
        'News' => [
            'table' => 'latest_news',
            'special' => 'false',
            'columns' => [
                'Date' => 'date',
                'News' => 'md_file'
            ]
        ],
        'Publications' => [
            'table' => 'publications',
            'special' => 'false',
            'columns' => [
                'Type' => 'publication_type_id',
                'Year' => 'year',
                'Title' => 'title',
                'Description' => 'md_file',
                'URL' => 'url'
            ]
        ],
        'Reviews& Quotes' => [
            'table' => 'reviews',
            'special' => true,
            'columns' => [
                'Text of Review/Quote' => 'md_file',
                'Attribution' => 'md_file'
            ]
        ],
        'See-Hear-Read' => [
            'table' => 'finds',
            'columns' => [
                'Text (highlighted as link to URL' => 'title',
                'URL' => 'url',
                'Date' => 'date',
                'Note (follows highlighted text)' => 'md_file',
                'Type' => 'find_type_id'
            ]
        ]
    ];

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

    private function insertData($index, $sheetName)
    {
        $map = $this->maps[$sheetName];
        return ['map' => $map];
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

            $output[$sheetName] = $this->insertData($index, $sheetName);
        }

        return response()->json(['data' => $output]);
    }
}
