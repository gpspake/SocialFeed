<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GetInstagramPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:getInstagramPosts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Instagram Posts';

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
     * Get Instagram posts as array
     *
     * @param $user
     * @return mixed
     */
    function getInstagramPosts($user)
    {
        $url = 'https://api.instagram.com/v1/users/' . $user . '/media/recent';
        $token = 'access_token=' . $user . '.' . env('instagram_access_token');
        $count = 'count=20';

        $query_string = '?' . $token . '&' . $count;

        $posts = json_decode( file_get_contents( $url . $query_string ), true );

        return $posts['data'];
    }

    /**
     * Format single Instagram post
     *
     * @param $ig_post
     */
    function parseInstagramPost($ig_post){
        $created_at = date('Y-m-d H:i:s', $ig_post['created_time']);
        $content = array_key_exists('caption', $ig_post) ? substr( $ig_post['caption']['text'] , 0 , 999 ) : '';


        $parsed_ig_post = [];
        $parsed_ig_post['service'] = 'instagram';
        $parsed_ig_post['post_id'] = $ig_post['id'];
        $parsed_ig_post['image_url'] = $ig_post['images']['low_resolution']['url'];
        $parsed_ig_post['content'] = $content;
        $parsed_ig_post['post_url'] = $ig_post['link'];
        $parsed_ig_post['published_at'] = $created_at;

        $this->storeInstagramPost($parsed_ig_post);
    }

    /**
     * Delete existing facebook posts from database
     */
    function deleteExistingInstagramPosts()
    {
        DB::table('feeds')->where('service', 'instagram')->delete();
    }

    /**
     * @param $post
     */
    function storeInstagramPost($post)
    {
        DB::table('feeds')->insert( $post );
    }

    function updateInstagramPosts()
    {
        $this->deleteExistingInstagramPosts();

        $posts = $this->getInstagramPosts( env('instagram_user_id') );

        array_map( array($this, 'parseInstagramPost' ), $posts );
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->updateInstagramPosts();
    }
}
