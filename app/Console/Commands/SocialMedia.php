<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;

trait SocialMedia {

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
     * Save posts
     */
    function savePosts()
    {
        $this->deleteExistingPosts();

        $posts = $this->getPosts();
        
        array_map( array($this, 'parsePost' ), $posts );
    }

}