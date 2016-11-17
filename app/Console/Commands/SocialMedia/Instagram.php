<?php

namespace App\Console\Commands\SocialMedia;

trait Instagram
{

    use Storage;

    /**
     * The service name
     *
     * @var string
     */
    protected $service_name = 'instagram';

    /**
     * Get Instagram posts as array
     *
     * @return mixed
     */
    function getPosts()
    {
        $url = 'https://api.instagram.com/v1/users/' . env('instagram_user_id') . '/media/recent';
        $token = 'access_token=' . env('instagram_user_id') . '.' . env('instagram_access_token');
        $count = 'count=20';

        $query_string = '?' . $token . '&' . $count;

        $posts = json_decode( file_get_contents( $url . $query_string ), true );

        return $posts['data'];
    }

    /**
     * Format single Instagram post
     *
     * @param $ig_post
     * @return array
     */
    function parsePost($ig_post){
        $created_at = date('Y-m-d H:i:s', $ig_post['created_time']);
        $content = array_key_exists('caption', $ig_post) ? substr( $ig_post['caption']['text'] , 0 , 999 ) : '';
        
        $parsed_ig_post = [];
        $parsed_ig_post['service'] = 'instagram';
        $parsed_ig_post['post_id'] = $ig_post['id'];
        $parsed_ig_post['image_url'] = $ig_post['images']['low_resolution']['url'];
        $parsed_ig_post['content'] = $content;
        $parsed_ig_post['post_url'] = $ig_post['link'];
        $parsed_ig_post['published_at'] = $created_at;

        return $parsed_ig_post;
    }
}
