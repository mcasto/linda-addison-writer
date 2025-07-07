<?php

namespace App\Http\Controllers;

use App\Models\Award;
use App\Models\BiblioType;
use App\Models\Cover;
use App\Models\Event;
use App\Models\Find;
use App\Models\FindType;
use App\Models\Freebie;
use App\Models\Honor;
use App\Models\LatestNews;
use App\Models\LessonsBlessing;
use App\Models\LifePoem;
use App\Models\OnlineResource;
use App\Models\PublicationType;
use App\Models\Review;
use App\Models\Social;
use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteData extends Controller
{
    public function pull(): JsonResponse
    {
        $data = [
            'honors' => Honor::orderBy('year', 'desc')->get(),
        ];

        return response()->json($data);
    }
}
