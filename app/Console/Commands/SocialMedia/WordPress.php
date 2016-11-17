<?php

namespace App\Console\Commands\SocialMedia;

trait WordPress
{

    use Storage;

    /**
     * Get tweets as array
     *
     * @return mixed
     * @throws \Exception
     */
    function getPosts()
    {
        $url = 'https://news.uthsc.edu/wp-json/wp/v2/posts?per_page=20&_embed';
        return json_decode( file_get_contents($url), true );
    }

    function parsePosts($posts)
    {
        return array_map(array($this, 'parsePost'), $posts);
    }

    /**
     * Format a single wordpress post
     * @param $wp_post
     * @return array
     */
    function parsePost($wp_post)
    {
        $media = array_key_exists('wp:featuredmedia', $wp_post['_embedded']) ? $wp_post['_embedded']['wp:featuredmedia'][0]['media_details']['sizes']['thumbnail']['source_url'] : '';
        $created_at = date('Y-m-d H:i:s', strtotime($wp_post['date']));
        $content = $wp_post['title']['rendered'];

        $parsed_tweet = [];
        $parsed_tweet['service'] = $this->service_name;
        $parsed_tweet['post_id'] = $wp_post['id'];
        $parsed_tweet['image_url'] = $media;
        $parsed_tweet['content'] = $content;
        $parsed_tweet['post_url'] = $wp_post['link'];
        $parsed_tweet['published_at'] = $created_at;

        return $parsed_tweet;
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