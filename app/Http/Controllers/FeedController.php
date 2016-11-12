<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Feed;
use Illuminate\Support\Facades\DB;

class FeedController extends Controller
{
    public function show()
    {
        $services = request()->service ? explode( ',', request()->service ) : 0;

        if ($services) {
            $feed = DB::table('feeds')
                ->whereIn('service', $services)
                ->orderBy('published_at', 'desc')
                ->get();
        } else {
            $feed = DB::table('feeds')
                ->orderBy('published_at', 'desc')
                ->get();
        }

        return $feed;
    }
}
