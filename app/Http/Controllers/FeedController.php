<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Feed;

class FeedController extends Controller
{
    public function show()
    {
        $services = request()->service ? explode( ',', request()->service ) : 0;

        $feed = $services ? Feed::all()->whereIn('service',$services) : Feed::all();

        return $feed;
    }
}
