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
            'awards' => Award::orderBy('year', 'desc')->get(),
            'biblio' => BiblioType::with('biblios')->with('biblios.biblio_pubs')->get(),
            'bio' => [
                'longer' => Markdown::convert(Storage::disk('local')->get('bio/longer.md'))->getContent(),
                'short' => Markdown::convert(Storage::disk('local')->get('bio/short.md'))->getContent(),
                'shortest' => Markdown::convert(Storage::disk('local')->get('bio/shortest.md'))->getContent(),
            ],
            'covers' => Cover::orderBy('sort_order')->get(),
            'events' => Event::orderBy('start_date', 'desc')->get(),
            'freebie' => Freebie::today(),
            'honors' => Honor::orderBy('year', 'desc')->get(),
            'latest_news' => LatestNews::orderBy('date', 'desc')->get(),
            'lessons_blessings' => LessonsBlessing::all(),
            'life_poem' => LifePoem::today(),
            'online_resources' => OnlineResource::with('online_resource_links')->get(),
            'publications' => PublicationType::with('publications')->get(),
            'reviews' => Review::all(),
            'finds' => FindType::with('finds')->get(),
            'socials' => Social::all()
        ];
        return response()->json($data);
    }
}
