<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use TwitterAPIExchange;
use Illuminate\Support\Facades\DB;

class GetTweets extends Command
{
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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

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

    function getTweets($user)
    {
        $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
        $getField = '?screen_name=' . $user;
        $twitter = new TwitterAPIExchange($this->getTwitterSettings());
        $response = $twitter->setGetfield($getField)
            ->buildOauth($url, 'GET')
            ->performRequest();
        return json_decode($response, true);
    }

    function remove_emoji($text){
        $regex = '/([0-9#][\x{20E3}])|[\x{00ae}\x{00a9}\x{203C}\x{2047}\x{2048}\x{2049}\x{3030}\x{303D}\x{2139}\x{2122}\x{3297}\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u';
        return preg_replace($regex, '', $text);
    }

    function parseTweet($tweet)
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

    function saveTweets()
    {
        $this->deleteExistingTweets();

        $tweets = $this->getTweets( env('twitter_user', '') );

        array_map( array($this, 'parseTweet' ), $tweets );
    }

    function deleteExistingTweets()
    {
        DB::table('feeds')->where('service', 'twitter')->delete();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->saveTweets();
    }
}
