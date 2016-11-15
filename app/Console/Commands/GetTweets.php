<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use TwitterAPIExchange;
use Illuminate\Support\Facades\DB;

class GetTweets extends Command
{

    use SocialMedia;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:getTweets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get my tweets';

    /**
     * The service name
     *
     * @var string
     */
    protected $service_name = 'twitter';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get twitter oath settings
     *
     * @return array
     */
    function getTwitterSettings()
    {
        $settings = array(
            'oauth_access_token' => env('twitter_oauth_access_token', ''),
            'oauth_access_token_secret' => env('twitter_oauth_access_token_secret', ''),
            'consumer_key' => env('twitter_consumer_key', ''),
            'consumer_secret' => env('twitter_consumer_secret', ''),
        );
        return $settings;
    }

    /**
     * Get tweets as array
     *
     * @return mixed
     * @throws \Exception
     */
    function getPosts()
    {
        $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';

        $screenName = 'screen_name=' . env('twitter_user');
        $excludeReplies = 'exclude_replies=true';
        $count = 'count=100';
        $includeRetweets = 'include_rts=false';

        $getField = '?' . $screenName . '&' . $excludeReplies . '&' . $count . '&' . $includeRetweets;
        $twitter = new TwitterAPIExchange($this->getTwitterSettings());
        $response = $twitter->setGetfield($getField)
            ->buildOauth($url, 'GET')
            ->performRequest();
        return json_decode($response, true);
    }

    /**
     * Remove emojis from a string
     *
     * @param $text
     * @return mixed
     */
    function remove_emoji($text){
        $regex = '/([0-9#][\x{20E3}])|[\x{00ae}\x{00a9}\x{203C}\x{2047}\x{2048}\x{2049}\x{3030}\x{303D}\x{2139}\x{2122}\x{3297}\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u';
        return preg_replace($regex, '', $text);
    }

    /**
     * Format a single tweet
     *
     * @param $tweet
     */
    function parsePost($tweet)
    {
        $media = array_key_exists ( 'media', $tweet['entities'] ) ? $tweet['entities']['media'][0]['media_url'] : '';
        $created_at = date('Y-m-d H:i:s', strtotime($tweet['created_at']));
        $content = substr( $this->remove_emoji($tweet['text']) , 0 , 999 );

        $parsed_tweet = [];
        $parsed_tweet['service'] = 'twitter';
        $parsed_tweet['post_id'] = $tweet['id_str'];
        $parsed_tweet['image_url'] = $media;
        $parsed_tweet['content'] = $content;
        $parsed_tweet['post_url'] = 'https://twitter.com/statuses/' . $tweet['id_str'];
        $parsed_tweet['published_at'] = $created_at;

        DB::table('feeds')->insert( $parsed_tweet );
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->savePosts();
    }
}
