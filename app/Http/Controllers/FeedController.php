<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Feed;

class FeedController extends Controller
{
    public function show()
    {
        return Feed::all();
    }
}
