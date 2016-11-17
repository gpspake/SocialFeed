<?php

namespace App\SocialMedia;

trait Facebook
{

    use Storage;

    /**
     * The service name
     *
     * @var string
     */
    protected $service_name = 'facebook';

    /**
     * Get fb posts as array
     *
     * @return mixed
     */
    public function getPosts()
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
     * Parse single fb post
     *
     * @param $fb_post
     * @return array
     */
    function parsePost($fb_post)
    {
        $created_at = date('Y-m-d H:i:s', strtotime($fb_post['created_time']));
        $content = array_key_exists('message', $fb_post) ? substr( $fb_post['message'] , 0 , 999 ) : '';
        $image = array_key_exists('image_url', $fb_post) ? $fb_post['image_url'] : '';

        $parsed_fb_post = [];
        $parsed_fb_post['service'] = 'facebook';
        $parsed_fb_post['post_id'] = $fb_post['id'];
        $parsed_fb_post['image_url'] = $image;
        $parsed_fb_post['content'] = $content;
        $parsed_fb_post['post_url'] = $fb_post['permalink_url'];
        $parsed_fb_post['published_at'] = $created_at;

        return $parsed_fb_post;
    }

    /**
     * @param $posts
     * @return bool
     */
    function valid($posts)
    {
        if( !is_array($posts)  ) {
            return false;
        }

        return true;
    }
}
