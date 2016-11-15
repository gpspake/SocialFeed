<?php

namespace App\Console\Commands\SocialMedia;

use Illuminate\Support\Facades\DB;

trait Storage {

    /**
     * Remove all existing posts from a service
     */
    function deleteExistingPosts()
    {
        DB::table('feeds')
            ->where('service', $this->service_name)
            ->delete();
    }

    /**
     * Save a single post to the database
     * 
     * @param $post
     */
    function savePost($post)
    {
        DB::table('feeds')->insert( $post );
    }

    /**
     * Save posts
     *
     * @param $posts
     */
    function savePosts($posts)
    {
        array_map(array($this, 'savePost'), $posts);
    }

}