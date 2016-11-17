<?php

namespace App\SocialMedia;

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
     * @param $posts
     * @return array
     */
    function parsePosts($posts)
    {
        return array_map(array($this, 'parsePost'), $posts);
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

    function handleError($error)
    {
        $subject = 'something\'s wrong with ' . $this->service_name;
        $message = 'error:' . "\n" . json_encode($error);

        //todo: email error message
        //todo: log error message

        echo $error;
    }

    function updatePosts()
    {
        try {
            $posts = $this->getPosts();

            if ( $this->valid($posts)  ) {
                $this->deleteExistingPosts();
                $this->savePosts( $this->parsePosts( $posts ) );
            } else {
                $this->handleError( $posts );
            }

        } catch (\Exception $exception) {
            $this->handleError( $exception->getMessage() );
        }
    }

}