<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GetFacebookPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:getFacebookPosts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Get fb posts as array
     *
     * @return mixed
     */
    public function getFacebookPosts()
    {
        $url = 'https://graph.facebook.com/uthsc/posts';
        $access_token = 'access_token=' . env('FACEBOOK_APP_ID') . '|' . env('FACEBOOK_APP_SECRET');
        $fields = 'fields=message,created_time,full_picture,permalink_url';
        $limit = 'limit=80';
        $query_string = '?' . $access_token . '&' . $fields . '&' . $limit;

        $posts = json_decode( file_get_contents ( $url . $query_string ), true );

        return $posts['data'];
    }

    /**
     * Parse single fb posts
     *
     * @param $fb_post
     */
    function parseFacebookPost($fb_post)
    {
        $created_at = date('Y-m-d H:i:s', strtotime($fb_post['created_time']));
        $message = array_key_exists('message', $fb_post) ? $fb_post['message'] : '';
        $image = array_key_exists('image_url', $fb_post) ? $fb_post['image_url'] : '';

        $parsed_fb_post = [];
        $parsed_fb_post['service'] = 'facebook';
        $parsed_fb_post['post_id'] = $fb_post['id'];
        $parsed_fb_post['image_url'] = $image;
        $parsed_fb_post['content'] = $message;
        $parsed_fb_post['post_url'] = $fb_post['permalink_url'];
        $parsed_fb_post['published_at'] = $created_at;

        $this->storeFacebookPost($parsed_fb_post);

    }

    /**
     * Store single fb post in database
     *
     * @param $post
     */
    function storeFacebookPost($post)
    {
        DB::table('feeds')->insert( $post );

    }

    function deleteExistingFacebookPosts()
    {
        DB::table('feeds')->where('service', 'facebook')->delete();
    }

    function updateFacebookPosts()
    {
        $this->deleteExistingFacebookPosts();

        $fb_posts = $this->getFacebookPosts();

        array_map( array($this, 'parseFacebookPost' ), $fb_posts );
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->updateFacebookPosts();
    }
}
